<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hrd_daily_reports', function (Blueprint $table) {
            $table->id();

            // Pembuat Laporan (HRD / Admin) — SET NULL jika akun dihapus
            $table->foreignId('admin_hrd_user_id')
                ->nullable()
                ->constrained('admin_hrd_users')
                ->nullOnDelete();
            $table->string('creator_name_snapshot');

            // Informasi Utama
            $table->date('report_date');
            $table->string('report_type'); // 'employee' atau 'office'

            // Identitas Karyawan (hanya jika report_type = 'employee')
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();
            $table->string('employee_name')->nullable();

            // Data Kategori Monitoring
            $table->json('monitoring_data');

            $table->timestamps();

            // Indeks untuk pencarian
            $table->index('report_date');
            $table->index('report_type');
            $table->index('admin_hrd_user_id');
            $table->index(['report_date', 'report_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrd_daily_reports');
    }
};
