<?php

namespace Tests\Feature;

use App\Models\DailyReport;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyReportSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private Division $teknisiDiv;

    private Division $projectDiv;

    private Division $salesDiv;

    private Employee $activeEmployee;

    private Employee $inactiveEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teknisiDiv = Division::create(['name' => 'Teknisi', 'code' => 'teknisi']);
        $this->projectDiv = Division::create(['name' => 'Admin Project', 'code' => 'admin_project']);
        $this->salesDiv = Division::create(['name' => 'Admin Sales', 'code' => 'admin_sales']);

        $this->activeEmployee = Employee::create([
            'division_id' => $this->teknisiDiv->id,
            'name' => 'Budi Pratama',
            'is_active' => true,
        ]);

        $this->inactiveEmployee = Employee::create([
            'division_id' => $this->teknisiDiv->id,
            'name' => 'Doni Nonaktif',
            'is_active' => false,
        ]);
    }

    public function test_public_report_page_can_be_rendered(): void
    {
        $response = $this->get(route('report.create'));

        $response->assertStatus(200);
        $response->assertSee('Daily Report');
        $response->assertSee('Teknisi');
    }

    public function test_api_employees_returns_only_active_employees_for_division(): void
    {
        $response = $this->getJson(route('api.employees', ['division_id' => $this->teknisiDiv->id]));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Budi Pratama']);
        $response->assertJsonMissing(['name' => 'Doni Nonaktif']);
    }

    public function test_employee_can_submit_teknisi_report(): void
    {
        $payload = [
            'division_id' => $this->teknisiDiv->id,
            'employee_id' => $this->activeEmployee->id,
            'report_date' => '2026-09-17',
            'email' => 'budi@kantor.com',
            'form_data' => [
                'pekerjaan_hari_ini' => ['instalasi', 'maintenance'],
                'status_pekerjaan' => 'selesai',
                'kendala' => 'Tidak ada kendala',
                'rencana_besok' => 'Maintenance rack server gedung B',
            ],
        ];

        $response = $this->post(route('report.store'), $payload);

        $response->assertRedirect(route('report.success'));
        $this->assertDatabaseHas('daily_reports', [
            'employee_id' => $this->activeEmployee->id,
            'division_id' => $this->teknisiDiv->id,
            'report_date' => '2026-09-17 00:00:00',
            'email' => 'budi@kantor.com',
            'employee_name_snapshot' => 'Budi Pratama',
            'division_name_snapshot' => 'Teknisi',
            'division_code_snapshot' => 'teknisi',
            'status' => 'active',
        ]);
    }

    public function test_employee_cannot_submit_duplicate_report_for_same_date(): void
    {
        DailyReport::create([
            'employee_id' => $this->activeEmployee->id,
            'division_id' => $this->teknisiDiv->id,
            'report_date' => '2026-09-17',
            'email' => 'budi@kantor.com',
            'employee_name_snapshot' => 'Budi Pratama',
            'division_name_snapshot' => 'Teknisi',
            'division_code_snapshot' => 'teknisi',
            'form_version' => 1,
            'status' => 'active',
            'form_data' => ['pekerjaan_hari_ini' => ['instalasi'], 'status_pekerjaan' => 'selesai'],
            'submitted_at' => now(),
        ]);

        $payload = [
            'division_id' => $this->teknisiDiv->id,
            'employee_id' => $this->activeEmployee->id,
            'report_date' => '2026-09-17',
            'email' => 'budi@kantor.com',
            'form_data' => [
                'pekerjaan_hari_ini' => ['maintenance'],
                'status_pekerjaan' => 'selesai',
            ],
        ];

        $response = $this->post(route('report.store'), $payload);

        $response->assertSessionHasErrors('report_date');
    }

    public function test_admin_project_document_tidak_ada_cannot_coexist_with_other_documents(): void
    {
        $projectEmp = Employee::create([
            'division_id' => $this->projectDiv->id,
            'name' => 'Siti Project',
            'is_active' => true,
        ]);

        $payload = [
            'division_id' => $this->projectDiv->id,
            'employee_id' => $projectEmp->id,
            'report_date' => '2026-09-17',
            'email' => 'siti@kantor.com',
            'form_data' => [
                'pekerjaan_hari_ini' => 'Supervisi instalasi jaringan kabel',
                'nama_project' => 'Gedung Wisma 46',
                'progres_persen' => 75,
                'dokumen_diproses' => ['invoice', 'tidak_ada'],
            ],
        ];

        $response = $this->post(route('report.store'), $payload);

        $response->assertSessionHasErrors('form_data.dokumen_diproses');
    }

    public function test_admin_sales_other_platform_requires_explanation(): void
    {
        $salesEmp = Employee::create([
            'division_id' => $this->salesDiv->id,
            'name' => 'Rina Sales',
            'is_active' => true,
        ]);

        $payload = [
            'division_id' => $this->salesDiv->id,
            'employee_id' => $salesEmp->id,
            'report_date' => '2026-09-17',
            'email' => 'rina@kantor.com',
            'form_data' => [
                'pekerjaan_hari_ini' => ['lainnya'],
                'pekerjaan_hari_ini_lainnya' => '', // Empty explanation must fail
                'rencana_besok' => ['follow_up'],
                'jumlah_customer' => 10,
                'jumlah_quotation' => 4,
                'jumlah_closing' => 2,
            ],
        ];

        $response = $this->post(route('report.store'), $payload);

        $response->assertSessionHasErrors('form_data.pekerjaan_hari_ini_lainnya');
    }
}
