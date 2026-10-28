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
     * Relasi ke karyawan yang dimonitor (jika report_type = 'employee').
     *
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
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
