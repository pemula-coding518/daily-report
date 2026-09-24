<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AppTimezoneTest extends TestCase
{
    use RefreshDatabase;

    private const SHIFT_MIGRATION = '2026_09_24_000000_shift_legacy_timestamps_to_wib';

    public function test_application_uses_jakarta_timezone(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        $this->assertSame('Asia/Jakarta', date_default_timezone_get());
    }

    public function test_new_report_submissions_store_wib_wall_clock_time(): void
    {
        $division = Division::create(['name' => 'Teknisi', 'code' => 'teknisi']);
        $employee = Employee::create([
            'division_id' => $division->id,
            'name' => 'Budi Pratama',
            'is_active' => true,
        ]);

        $before = now();

        $response = $this->post(route('report.store'), [
            'division_id' => $division->id,
            'employee_id' => $employee->id,
            'report_date' => '2026-09-17',
            'email' => 'budi@kantor.com',
            'form_data' => [
                'work_items' => [
                    [
                        'type' => 'instalasi',
                        'custom_type' => '',
                        'detail' => 'Instalasi access point di lantai 2',
                        'status' => 'selesai',
                    ],
                ],
            ],
        ]);

        $after = now();

        $response->assertRedirect(route('report.success'));

        $report = DailyReport::query()->firstOrFail();

        // Epoch sanity check.
        $this->assertGreaterThanOrEqual($before->getTimestamp(), $report->submitted_at->getTimestamp());
        $this->assertLessThanOrEqual($after->getTimestamp(), $report->submitted_at->getTimestamp());

        // Wall-clock check against the raw stored value: the submission time
        // formatted in the application timezone (Asia/Jakarta) must equal what
        // is physically stored. If the app ever reverts to storing UTC, the
        // stored hour would be 7 hours behind and this would fail.
        $raw = DB::table('daily_reports')->where('id', $report->id)->first();

        $this->assertSame(
            $before->format('H:i'),
            substr((string) $raw->submitted_at, 11, 5),
            'Stored submitted_at wall clock must match the Asia/Jakarta time of submission.'
        );
    }

    public function test_legacy_utc_timestamps_are_shifted_to_wib(): void
    {
        // RefreshDatabase already ran the shift migration on empty tables.
        // Remove its record so it can be re-run against simulated legacy rows.
        DB::table('migrations')->where('migration', self::SHIFT_MIGRATION)->delete();

        // Simulate rows written by the pre-fix application, which stored UTC:
        // 08:37:36 UTC == 15:37:36 WIB.
        $division = $this->createDivision();
        $reportId = DB::table('daily_reports')->insertGetId([
            'employee_id' => $this->createEmployee($division)->id,
            'division_id' => $division->id,
            'report_date' => '2026-09-17 00:00:00',
            'email' => 'legacy@kantor.com',
            'employee_name_snapshot' => 'Karyawan Lama',
            'division_name_snapshot' => 'Teknisi',
            'division_code_snapshot' => 'teknisi',
            'form_version' => 1,
            'status' => 'active',
            'form_data' => json_encode(['pekerjaan_hari_ini' => 'Legacy']),
            'submitted_at' => '2026-09-17 08:37:36',
            'created_at' => '2026-09-17 03:37:36',
            'updated_at' => '2026-09-17 03:37:36',
        ]);

        $this->artisan('migrate', [
            '--path' => 'database/migrations/2026_09_24_000000_shift_legacy_timestamps_to_wib.php',
            '--force' => true,
        ])->assertExitCode(0);

        $raw = DB::table('daily_reports')->where('id', $reportId)->first();

        $this->assertSame('2026-09-17 15:37:36', (string) $raw->submitted_at);
        $this->assertSame('2026-09-17 10:37:36', (string) $raw->created_at);
        $this->assertSame('2026-09-17 10:37:36', (string) $raw->updated_at);

        // down() must revert the shift so the migration is reversible.
        $this->artisan('migrate:rollback', ['--step' => 1, '--force' => true])
            ->assertExitCode(0);

        $raw = DB::table('daily_reports')->where('id', $reportId)->first();

        $this->assertSame('2026-09-17 08:37:36', (string) $raw->submitted_at);
        $this->assertSame('2026-09-17 03:37:36', (string) $raw->created_at);

        // Re-apply so the schema state stays migrated for consistency.
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
    }

    private function createDivision(): Division
    {
        return Division::create(['name' => 'Teknisi', 'code' => 'teknisi']);
    }

    private function createEmployee(Division $division): Employee
    {
        return Employee::create([
            'division_id' => $division->id,
            'name' => 'Karyawan Legacy',
            'is_active' => true,
        ]);
    }
}
