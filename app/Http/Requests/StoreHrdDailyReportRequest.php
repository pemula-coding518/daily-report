<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreHrdDailyReportRequest extends FormRequest
{
    /**
     * Kategori monitoring karyawan yang diizinkan.
     *
     * @var list<string>
     */
    public const EMPLOYEE_CATEGORIES = [
        'kedisiplinan_karyawan',
        'sikap_perilaku',
        'aktivitas_tanggung_jawab',
        'komunikasi_koordinasi',
        'kepatuhan_aturan',
        'kerja_sama',
        'kendala_permasalahan',
        'hal_positif_apresiasi',
        'temuan_lainnya',
    ];

    /**
     * Kategori monitoring kondisi kantor yang diizinkan.
     *
     * @var list<string>
     */
    public const OFFICE_CATEGORIES = [
        'kebersihan_kerapian',
        'fasilitas_peralatan',
        'keamanan_kenyamanan_k3',
    ];

    /**
     * Pilihan tindakan HRD untuk kondisi kantor.
     *
     * @var list<string>
     */
    public const HRD_ACTIONS = [
        'tidak_perlu_tindakan',
        'mengingatkan_karyawan',
        'koordinasi_atasan',
        'melaporkan_kerusakan',
        'mengajukan_perbaikan',
    ];

    /**
     * Pilihan Status Tindak Lanjut HRD untuk kategori monitoring karyawan.
     *
     * @var array<string, string>
     */
    public const FOLLOW_UP_STATUSES = [
        'selesai' => 'Selesai',
        'dalam_proses' => 'Dalam proses',
        'menunggu_pihak_terkait' => 'Menunggu pihak terkait',
        'perlu_monitoring_lanjutan' => 'Perlu monitoring lanjutan',
        'memerlukan_keputusan_direktur' => 'Memerlukan keputusan Direktur',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Siapkan input sebelum validasi untuk menentukan report_type secara otomatis jika belum diisi.
     */
    protected function prepareForValidation(): void
    {
        $hasEmployees = ! empty($this->input('employees'));
        $hasOffice = ! empty($this->input('office_items'));

        $inputReportType = $this->input('report_type');

        if ($hasEmployees && $hasOffice) {
            $this->merge(['report_type' => 'both']);
        } elseif ($hasEmployees && empty($inputReportType)) {
            $this->merge(['report_type' => 'employee']);
        } elseif ($hasOffice && empty($inputReportType)) {
            $this->merge(['report_type' => 'office']);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $reportType = $this->input('report_type');

        $rules = [
            'report_date' => ['required', 'date'],
            'report_type' => ['required', Rule::in(['employee', 'office', 'both'])],
        ];

        if ($reportType === 'employee' || $reportType === 'both') {
            $rules['employees'] = ['required', 'array', 'min:1'];
            $rules['employees.*.input_mode'] = ['required', 'in:select,manual'];
            $rules['employees.*.employee_id'] = [
                'nullable',
                'integer',
                Rule::exists('employees', 'id')->where('is_active', true),
            ];
            $rules['employees.*.manual_employee_name'] = [
                'nullable',
                'string',
                'min:2',
                'max:255',
            ];
            $rules['employees.*.categories'] = ['required', 'array', 'min:1'];
            $rules['employees.*.categories.*.category'] = [
                'required',
                'string',
                Rule::in(self::EMPLOYEE_CATEGORIES),
            ];
            $rules['employees.*.categories.*.follow_up_status'] = [
                'required',
                'string',
                Rule::in(array_keys(self::FOLLOW_UP_STATUSES)),
            ];
            $rules['employees.*.categories.*.notes'] = ['required', 'string'];
        }

        if ($reportType === 'office' || $reportType === 'both') {
            $rules['office_items'] = ['required', 'array', 'min:1'];
            $rules['office_items.*.category'] = ['required', 'string', Rule::in(self::OFFICE_CATEGORIES)];
            $rules['office_items.*.finding'] = ['required', 'string'];
            $rules['office_items.*.action'] = ['required', 'string', Rule::in(self::HRD_ACTIONS)];
            $rules['office_items.*.action_notes'] = ['required', 'string'];
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $reportType = $this->input('report_type');
            $hasEmployees = ! empty($this->input('employees'));
            $hasOffice = ! empty($this->input('office_items'));

            if (! $hasEmployees && ! $hasOffice) {
                $validator->errors()->add(
                    'report_type',
                    'Minimal salah satu jenis monitoring (Monitoring Karyawan atau Monitoring Kondisi Kantor) wajib diisi.'
                );

                return;
            }

            // Validasi detail karyawan jika bagian karyawan disertakan
            if ($reportType === 'employee' || $reportType === 'both' || $hasEmployees) {
                $employees = (array) $this->input('employees', []);
                $seenEmployeeIds = [];

                foreach ($employees as $empIndex => $empData) {
                    if (! is_array($empData)) {
                        continue;
                    }

                    $inputMode = $empData['input_mode'] ?? 'select';

                    if ($inputMode === 'select') {
                        if (empty($empData['employee_id'])) {
                            $validator->errors()->add(
                                "employees.{$empIndex}.employee_id",
                                'Nama karyawan wajib dipilih dari daftar.'
                            );
                        } else {
                            $empId = $empData['employee_id'];
                            if (in_array($empId, $seenEmployeeIds)) {
                                $validator->errors()->add(
                                    "employees.{$empIndex}.employee_id",
                                    'Karyawan ini sudah ditambahkan sebelumnya dalam laporan yang sama.'
                                );
                            }
                            $seenEmployeeIds[] = $empId;
                        }
                    } else {
                        if (empty(trim((string) ($empData['manual_employee_name'] ?? '')))) {
                            $validator->errors()->add(
                                "employees.{$empIndex}.manual_employee_name",
                                'Nama karyawan manual wajib diisi.'
                            );
                        }
                    }

                    // Validasi kategori duplikat pada karyawan yang sama
                    $categories = (array) ($empData['categories'] ?? []);
                    $seenCategories = [];
                    foreach ($categories as $catIndex => $catItem) {
                        if (is_array($catItem)) {
                            $cat = $catItem['category'] ?? '';
                            if (in_array($cat, $seenCategories)) {
                                $validator->errors()->add(
                                    "employees.{$empIndex}.categories.{$catIndex}.category",
                                    'Kategori tidak boleh dipilih lebih dari satu kali untuk karyawan ini.'
                                );
                            }
                            $seenCategories[] = $cat;
                        }
                    }
                }
            }

            // Validasi detail kantor jika bagian kantor disertakan
            if ($reportType === 'office' || $reportType === 'both' || $hasOffice) {
                $officeItems = (array) $this->input('office_items', []);
                $seen = [];
                foreach ($officeItems as $index => $item) {
                    if (is_array($item)) {
                        $cat = $item['category'] ?? '';
                        if (in_array($cat, $seen)) {
                            $validator->errors()->add(
                                "office_items.{$index}.category",
                                'Kategori tidak boleh dipilih lebih dari satu kali dalam satu laporan.'
                            );
                        }
                        $seen[] = $cat;
                    }
                }
            }
        });
    }

    /**
     * Custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'report_date' => 'Tanggal Laporan',
            'report_type' => 'Jenis Monitoring',
            'employees' => 'Daftar Karyawan',
            'employees.*.input_mode' => 'Mode Input Karyawan',
            'employees.*.employee_id' => 'Nama Karyawan',
            'employees.*.manual_employee_name' => 'Nama Karyawan (Manual)',
            'employees.*.categories' => 'Kategori Monitoring',
            'employees.*.categories.*.category' => 'Kategori',
            'employees.*.categories.*.follow_up_status' => 'Status Tindak Lanjut HRD',
            'employees.*.categories.*.notes' => 'Detail Catatan',
            'office_items' => 'Item Kondisi Kantor',
            'office_items.*.category' => 'Kategori Kantor',
            'office_items.*.finding' => 'Detail Temuan',
            'office_items.*.action' => 'Tindakan HRD',
            'office_items.*.action_notes' => 'Catatan Tindakan',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'report_type.required' => 'Jenis monitoring wajib ditentukan.',
            'report_type.in' => 'Jenis monitoring tidak valid.',
            'employees.required' => 'Minimal satu entri karyawan wajib ditambahkan.',
            'employees.min' => 'Minimal satu entri karyawan wajib ditambahkan.',
            'employees.*.categories.required' => 'Minimal satu kategori monitoring wajib dipilih untuk karyawan ini.',
            'employees.*.categories.min' => 'Minimal satu kategori monitoring wajib dipilih untuk karyawan ini.',
            'employees.*.categories.*.follow_up_status.required' => 'Status Tindak Lanjut HRD wajib dipilih untuk kategori ini.',
            'employees.*.categories.*.follow_up_status.in' => 'Pilihan Status Tindak Lanjut HRD tidak valid.',
            'office_items.required' => 'Minimal satu kategori kondisi kantor wajib dipilih.',
            'office_items.min' => 'Minimal satu kategori kondisi kantor wajib dipilih.',
            'employees.*.employee_id.exists' => 'Karyawan yang dipilih tidak valid atau tidak aktif.',
        ];
    }
}
