<?php

namespace App\Http\Requests;

use App\Models\DailyReport;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDailyReportRequest extends FormRequest
{
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
        $division = Division::find($this->input('division_id'));
        $code = strtolower((string) $division?->code);

        $rules = [
            'division_id' => ['required', 'exists:divisions,id'],
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],
            'email' => ['required', 'email'],
            'report_date' => ['required', 'date'],
            'form_data' => ['required', 'array'],
        ];

        // Specific division rules
        if ($code === 'teknisi') {
            $rules['form_data.pekerjaan_hari_ini'] = ['required', 'array', 'min:1'];
            $rules['form_data.status_pekerjaan'] = ['required', 'in:selesai,progres,pending'];
            $rules['form_data.kendala'] = ['nullable', 'string'];
            $rules['form_data.rencana_besok'] = ['nullable', 'string'];

            if (in_array('lainnya', (array) $this->input('form_data.pekerjaan_hari_ini', []))) {
                $rules['form_data.pekerjaan_lainnya'] = ['required', 'string', 'max:500'];
            }
        } elseif ($code === 'admin_sales') {
            $rules['form_data.pekerjaan_hari_ini'] = ['required', 'array', 'min:1'];
            $rules['form_data.rencana_besok'] = ['required', 'array', 'min:1'];
            $rules['form_data.jumlah_customer'] = ['required', 'integer', 'min:0'];
            $rules['form_data.jumlah_quotation'] = ['required', 'integer', 'min:0'];
            $rules['form_data.jumlah_closing'] = ['required', 'integer', 'min:0'];
            $rules['form_data.kendala'] = ['nullable', 'string'];

            if (in_array('lainnya', (array) $this->input('form_data.pekerjaan_hari_ini', []))) {
                $rules['form_data.pekerjaan_hari_ini_lainnya'] = ['required', 'string', 'max:500'];
            }
            if (in_array('lainnya', (array) $this->input('form_data.rencana_besok', []))) {
                $rules['form_data.rencana_besok_lainnya'] = ['required', 'string', 'max:500'];
            }
        } elseif ($code === 'admin_project') {
            $rules['form_data.pekerjaan_hari_ini'] = ['required', 'string'];
            $rules['form_data.nama_project'] = ['required', 'string'];
            $rules['form_data.progres_persen'] = ['required', 'integer', 'min:0', 'max:100'];
            $rules['form_data.dokumen_diproses'] = ['required', 'array', 'min:1'];
            $rules['form_data.kendala'] = ['nullable', 'string'];
            $rules['form_data.rencana_besok'] = ['nullable', 'string'];

            if (in_array('lainnya', (array) $this->input('form_data.dokumen_diproses', []))) {
                $rules['form_data.dokumen_lainnya'] = ['required', 'string', 'max:500'];
            }
        } elseif ($code === 'admin_procurement') {
            $rules['form_data.pekerjaan_hari_ini'] = ['required', 'string'];
            $rules['form_data.jumlah_po'] = ['required', 'integer', 'min:0'];
            $rules['form_data.vendor_dihubungi'] = ['nullable', 'string'];
            $rules['form_data.barang_diterima_dikirim'] = ['nullable', 'string'];
            $rules['form_data.kendala'] = ['nullable', 'string'];
            $rules['form_data.rencana_besok'] = ['nullable', 'string'];
        } elseif ($code === 'system_informasi') {
            $rules['form_data.pekerjaan_hari_ini'] = ['required', 'string'];
            $rules['form_data.status_pengerjaan'] = ['required', 'string'];
            $rules['form_data.kendala'] = ['nullable', 'string'];
            $rules['form_data.rencana_besok'] = ['nullable', 'string'];
        } elseif ($code === 'finance') {
            $rules['form_data.pekerjaan_hari_ini'] = ['required', 'string'];
            $rules['form_data.invoice_dibuat'] = ['nullable', 'string'];
            $rules['form_data.pembayaran'] = ['nullable', 'string'];
            $rules['form_data.rekap_kas_bank'] = ['nullable', 'string'];
            $rules['form_data.kendala'] = ['nullable', 'string'];
            $rules['form_data.rencana_besok'] = ['nullable', 'string'];
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $employeeId = $this->input('employee_id');
            $divisionId = $this->input('division_id');
            $reportDate = $this->input('report_date');

            if ($employeeId && $divisionId) {
                $employee = Employee::find($employeeId);
                if (! $employee || (int) $employee->division_id !== (int) $divisionId || ! $employee->is_active) {
                    $validator->errors()->add('employee_id', 'Karyawan yang dipilih tidak valid atau tidak aktif pada divisi ini.');
                }
            }

            if ($employeeId && $reportDate) {
                $exists = DailyReport::where('employee_id', $employeeId)
                    ->whereDate('report_date', $reportDate)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('report_date', 'Laporan untuk karyawan ini pada tanggal yang dipilih sudah pernah dibuat.');
                }
            }

            // Mutual exclusivity for Admin Project: "tidak_ada"
            $division = Division::find($divisionId);
            if (strtolower((string) $division?->code) === 'admin_project') {
                $docs = (array) $this->input('form_data.dokumen_diproses', []);
                if (in_array('tidak_ada', $docs) && count($docs) > 1) {
                    $validator->errors()->add('form_data.dokumen_diproses', 'Pilihan "Tidak ada" tidak boleh dipilih bersamaan dengan dokumen lain.');
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
            'division_id' => 'Divisi',
            'employee_id' => 'Nama Karyawan',
            'email' => 'Email',
            'report_date' => 'Tanggal Pekerjaan',
            'form_data.pekerjaan_hari_ini' => 'Pekerjaan Hari Ini',
            'form_data.pekerjaan_lainnya' => 'Penjelasan Pekerjaan Lainnya',
            'form_data.pekerjaan_hari_ini_lainnya' => 'Penjelasan Pekerjaan Hari Ini Lainnya',
            'form_data.rencana_besok' => 'Rencana Besok',
            'form_data.rencana_besok_lainnya' => 'Penjelasan Rencana Besok Lainnya',
            'form_data.status_pekerjaan' => 'Status Pekerjaan',
            'form_data.status_pengerjaan' => 'Status Pengerjaan',
            'form_data.jumlah_customer' => 'Jumlah Customer',
            'form_data.jumlah_quotation' => 'Jumlah Quotation',
            'form_data.jumlah_closing' => 'Jumlah Closing',
            'form_data.nama_project' => 'Nama Project',
            'form_data.progres_persen' => 'Progres Project',
            'form_data.dokumen_diproses' => 'Dokumen yang Diproses',
            'form_data.dokumen_lainnya' => 'Penjelasan Dokumen Lainnya',
            'form_data.jumlah_po' => 'Jumlah PO Dibuat',
        ];
    }
}
