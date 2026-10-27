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
     * Pilihan tindakan HRD yang diizinkan.
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
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'report_type' => ['required', 'in:employee,office'],
        ];

        if ($reportType === 'employee') {
            $rules['input_mode'] = ['required', 'in:select,manual'];

            if ($this->input('input_mode') === 'select') {
                $rules['employee_id'] = [
                    'required',
                    'integer',
                    Rule::exists('employees', 'id')->where('is_active', true),
                ];
            } else {
                $rules['manual_employee_name'] = ['required', 'string', 'min:2', 'max:255'];
            }

            $rules['categories'] = ['required', 'array', 'min:1'];
            $rules['categories.*.category'] = ['required', 'string', Rule::in(self::EMPLOYEE_CATEGORIES)];
            $rules['categories.*.notes'] = ['required', 'string'];
        }

        if ($reportType === 'office') {
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

            // Validasi kategori duplikat pada monitoring karyawan
            if ($reportType === 'employee') {
                $categories = (array) $this->input('categories', []);
                $seen = [];
                foreach ($categories as $index => $item) {
                    if (is_array($item)) {
                        $cat = $item['category'] ?? '';
                        if (in_array($cat, $seen)) {
                            $validator->errors()->add(
                                "categories.{$index}.category",
                                'Kategori tidak boleh dipilih lebih dari satu kali dalam satu laporan.'
                            );
                        }
                        $seen[] = $cat;
                    }
                }
            }

            // Validasi kategori duplikat pada monitoring kantor
            if ($reportType === 'office') {
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
            'input_mode' => 'Mode Input Karyawan',
            'employee_id' => 'Nama Karyawan',
            'manual_employee_name' => 'Nama Karyawan (Manual)',
            'categories' => 'Kategori Monitoring',
            'categories.*.category' => 'Kategori',
            'categories.*.notes' => 'Catatan Kategori',
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
            'categories.required' => 'Minimal satu kategori monitoring wajib dipilih.',
            'categories.min' => 'Minimal satu kategori monitoring wajib dipilih.',
            'office_items.required' => 'Minimal satu kategori kondisi kantor wajib dipilih.',
            'office_items.min' => 'Minimal satu kategori kondisi kantor wajib dipilih.',
            'employee_id.exists' => 'Karyawan yang dipilih tidak valid atau tidak aktif.',
            'manual_employee_name.required' => 'Nama karyawan wajib diisi.',
        ];
    }
}
