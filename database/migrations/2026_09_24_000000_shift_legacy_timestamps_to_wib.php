<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * All legacy timestamps were written while the application timezone was UTC.
 * The application now stores timestamps in Asia/Jakarta (WIB, UTC+7), so
 * existing values are shifted forward by 7 hours to match.
 *
 * Affected tables/columns (see $columnsByTable). Re-running is prevented by
 * Laravel's migration mechanism; the down() method shifts the values back.
 */
return new class extends Migration
{
    /**
     * Timestamp columns to shift, per table.
     *
     * @var array<string, list<string>>
     */
    private array $columnsByTable = [
        'users' => ['email_verified_at', 'created_at', 'updated_at'],
        'daily_reports' => ['submitted_at', 'created_at', 'updated_at'],
        'admin_hrd_users' => ['last_login_at', 'created_at', 'updated_at'],
        'employees' => ['created_at', 'updated_at'],
    ];

    public function up(): void
    {
        $this->shiftTimestamps('+');
    }

    public function down(): void
    {
        $this->shiftTimestamps('-');
    }

    private function shiftTimestamps(string $sign): void
    {
        foreach ($this->columnsByTable as $table => $columns) {
            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                $driver = DB::connection()->getDriverName();

                match ($driver) {
                    'mysql', 'mariadb' => DB::table($table)
                        ->whereNotNull($column)
                        ->update([$column => DB::raw("`{$column}` {$sign} INTERVAL 7 HOUR")]),
                    'pgsql' => DB::table($table)
                        ->whereNotNull($column)
                        ->update([$column => DB::raw("\"{$column}\" {$sign} INTERVAL '7 hours'")]),
                    'sqlite' => DB::table($table)
                        ->whereNotNull($column)
                        ->update([$column => DB::raw("datetime(\"{$column}\", '{$sign}7 hours')")]),
                    default => null,
                };
            }
        }
    }
};
