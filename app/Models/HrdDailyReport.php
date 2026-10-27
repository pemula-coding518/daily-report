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
}
