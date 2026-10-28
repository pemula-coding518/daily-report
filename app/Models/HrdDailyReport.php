<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrdDailyReport extends Model
{
    use HasFactory;

    protected $table = 'hrd_daily_reports';

    protected $fillable = [
        'admin_hrd_user_id',
        'creator_name_snapshot',
        'report_date',
        'report_type',
        'employee_id',
        'employee_name',
        'monitoring_data',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'monitoring_data' => 'array',
        ];
    }

    /**
     * Relasi ke akun pembuat laporan (Admin/HRD).
     *
     * @return BelongsTo<AdminHrdUser, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(AdminHrdUser::class, 'admin_hrd_user_id');
    }

    /**
     * Relasi ke karyawan yang dimonitor (jika ada relasi ke tabel Employee).
     *
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Mengecek apakah laporan ini mencakup data Monitoring Karyawan.
     */
    public function hasEmployeeMonitoring(): bool
    {
        if ($this->report_type === 'employee' || $this->report_type === 'both') {
            return true;
        }

        $data = is_array($this->monitoring_data) ? $this->monitoring_data : [];

        return ! empty($data['employees']);
    }

    /**
     * Mengecek apakah laporan ini mencakup data Monitoring Kondisi Kantor.
     */
    public function hasOfficeMonitoring(): bool
    {
        if ($this->report_type === 'office' || $this->report_type === 'both') {
            return true;
        }

        $data = is_array($this->monitoring_data) ? $this->monitoring_data : [];

        return ! empty($data['office_items']);
    }

    /**
     * Mendapatkan daftar entri karyawan dari monitoring_data (dengan backward compatibility).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEmployeesData(): array
    {
        $data = is_array($this->monitoring_data) ? $this->monitoring_data : [];

        // Format gabungan: ['employees' => [...], 'office_items' => [...]]
        if (isset($data['employees']) && is_array($data['employees'])) {
            return $data['employees'];
        }

        // Format employee-only
        if ($this->report_type === 'employee') {
            // Jika multi-employee format (array of employee objects)
            if (isset($data[0]['categories']) || isset($data[0]['employee_name'])) {
                return $data;
            }

            // Jika legacy flat format: array of category objects untuk satu karyawan
            return [
                [
                    'employee_id' => $this->employee_id,
                    'employee_name' => $this->employee_name,
                    'input_mode' => $this->employee_id ? 'select' : 'manual',
                    'categories' => $data,
                ],
            ];
        }

        return [];
    }

    /**
     * Mendapatkan daftar item kondisi kantor dari monitoring_data (dengan backward compatibility).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOfficeData(): array
    {
        $data = is_array($this->monitoring_data) ? $this->monitoring_data : [];

        // Format gabungan: ['employees' => [...], 'office_items' => [...]]
        if (isset($data['office_items']) && is_array($data['office_items'])) {
            return $data['office_items'];
        }

        // Format office-only: array of office item objects
        if ($this->report_type === 'office') {
            return $data;
        }

        return [];
    }

    /**
     * Helper untuk mendapatkan label Status Tindak Lanjut HRD.
     */
    public static function getFollowUpStatusLabel(?string $status): string
    {
        $statuses = [
            'selesai' => 'Selesai',
            'dalam_proses' => 'Dalam proses',
            'menunggu_pihak_terkait' => 'Menunggu pihak terkait',
            'perlu_monitoring_lanjutan' => 'Perlu monitoring lanjutan',
            'memerlukan_keputusan_direktur' => 'Memerlukan keputusan Direktur',
        ];

        return $statuses[$status] ?? ucwords(str_replace('_', ' ', $status ?? '-'));
    }

    /**
     * Helper untuk mendapatkan warna badge Status Tindak Lanjut HRD.
     */
    public static function getFollowUpStatusBadgeColor(?string $status): string
    {
        $colors = [
            'selesai' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'dalam_proses' => 'bg-sky-100 text-sky-800 border border-sky-200',
            'menunggu_pihak_terkait' => 'bg-amber-100 text-amber-800 border border-amber-200',
            'perlu_monitoring_lanjutan' => 'bg-purple-100 text-purple-800 border border-purple-200',
            'memerlukan_keputusan_direktur' => 'bg-rose-100 text-rose-800 border border-rose-200',
        ];

        return $colors[$status] ?? 'bg-slate-100 text-slate-700 border border-slate-200';
    }
}
