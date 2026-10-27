<?php

namespace Tests\Feature;

use App\Models\AdminHrdUser;
use App\Models\Division;
use App\Models\Employee;
use App\Models\HrdDailyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HrdDailyReportTest extends TestCase
{
    use RefreshDatabase;

    private AdminHrdUser $admin;

    private AdminHrdUser $hrd;

    private Division $division;

    private Employee $activeEmployee;

    private Employee $inactiveEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminHrdUser::create([
            'name' => 'Super Administrator',
            'email' => 'admin@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->hrd = AdminHrdUser::create([
            'name' => 'Staf HRD',
            'email' => 'hrd@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
            'is_active' => true,
        ]);

        $this->division = Division::create([
            'name' => 'Umum',
            'code' => 'umum',
        ]);

        $this->activeEmployee = Employee::create([
            'division_id' => $this->division->id,
            'name' => 'Budi Santoso',
            'is_active' => true,
        ]);

        $this->inactiveEmployee = Employee::create([
            'division_id' => $this->division->id,
            'name' => 'Andi Nonaktif',
            'is_active' => false,
        ]);
    }

    /**
     * 1. Guest diarahkan ke halaman login saat mengakses fitur HRD report.
     */
    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get(route('admin.hrd-reports.index'));
        $response->assertRedirect(route('admin.login'));

        $responseCreate = $this->get(route('admin.hrd-reports.create'));
        $responseCreate->assertRedirect(route('admin.login'));

        $responseStore = $this->post(route('admin.hrd-reports.store'), []);
        $responseStore->assertRedirect(route('admin.login'));
    }

    /**
     * 2. HRD dapat membuka halaman, menyimpan laporan, dan melihat detail.
     */
    public function test_hrd_can_view_index_create_report_and_view_show(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $indexRes = $this->get(route('admin.hrd-reports.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Riwayat Daily Report HRD');

        $createRes = $this->get(route('admin.hrd-reports.create'));
        $createRes->assertStatus(200);
        $createRes->assertSee('Buat Daily Report HRD');

        $payload = [
            'report_date' => '2026-10-27',
            'report_type' => 'employee',
            'input_mode' => 'select',
            'employee_id' => $this->activeEmployee->id,
            'categories' => [
                [
                    'category' => 'kedisiplinan_karyawan',
                    'notes' => 'Datang tepat waktu dan memimpin doa pagi.',
                ],
            ],
        ];

        $storeRes = $this->post(route('admin.hrd-reports.store'), $payload);
        $report = HrdDailyReport::latest('id')->first();
        $this->assertNotNull($report);
        $storeRes->assertRedirect(route('admin.hrd-reports.show', $report));

        $showRes = $this->get(route('admin.hrd-reports.show', $report));
        $showRes->assertStatus(200);
        $showRes->assertSee('Budi Santoso');
        $showRes->assertSee('Kedisiplinan Karyawan');
    }

    /**
     * 3. Admin dapat membuka halaman, menyimpan laporan, dan melihat detail.
     */
    public function test_admin_can_view_index_create_report_and_view_show(): void
    {
        $this->actingAs($this->admin, 'admin_hrd');

        $indexRes = $this->get(route('admin.hrd-reports.index'));
        $indexRes->assertStatus(200);

        $payload = [
            'report_date' => '2026-10-27',
            'report_type' => 'office',
            'office_items' => [
                [
                    'category' => 'kebersihan_kerapian',
                    'finding' => 'Ruang arsip rapi dan bersih.',
                    'action' => 'tidak_perlu_tindakan',
                    'action_notes' => 'Pemeriksaan rutin berjalan baik.',
                ],
            ],
        ];

        $storeRes = $this->post(route('admin.hrd-reports.store'), $payload);
        $report = HrdDailyReport::latest('id')->first();
        $this->assertNotNull($report);
        $storeRes->assertRedirect(route('admin.hrd-reports.show', $report));

        $showRes = $this->get(route('admin.hrd-reports.show', $report));
        $showRes->assertStatus(200);
        $showRes->assertSee('Kebersihan dan Kerapian');
    }

    /**
     * 4. Input karyawan dari master menyimpan ID serta snapshot yang benar.
     */
    public function test_employee_input_from_master_saves_id_and_snapshot(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'employee',
            'input_mode' => 'select',
            'employee_id' => $this->activeEmployee->id,
            'categories' => [
                [
                    'category' => 'sikap_perilaku',
                    'notes' => 'Sangat kooperatif dengan rekan kerja.',
                ],
            ],
        ]);

        $this->assertDatabaseHas('hrd_daily_reports', [
            'employee_id' => $this->activeEmployee->id,
            'employee_name' => 'Budi Santoso',
            'creator_name_snapshot' => $this->hrd->name,
            'report_type' => 'employee',
        ]);
    }

    /**
     * 5. Input manual menyimpan nama dan employee_id = null.
     */
    public function test_manual_employee_input_saves_null_id_and_manual_name(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'employee',
            'input_mode' => 'manual',
            'manual_employee_name' => 'Rian Magang IT',
            'categories' => [
                [
                    'category' => 'aktivitas_tanggung_jawab',
                    'notes' => 'Menyelesaikan modul pengujian dasar.',
                ],
            ],
        ]);

        $this->assertDatabaseHas('hrd_daily_reports', [
            'employee_id' => null,
            'employee_name' => 'Rian Magang IT',
            'report_type' => 'employee',
        ]);
    }

    /**
     * 6. Karyawan nonaktif atau ID tidak valid ditolak oleh backend.
     */
    public function test_inactive_or_invalid_employee_id_is_rejected(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        // Karyawan nonaktif
        $responseInactive = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'employee',
            'input_mode' => 'select',
            'employee_id' => $this->inactiveEmployee->id,
            'categories' => [
                [
                    'category' => 'kedisiplinan_karyawan',
                    'notes' => 'Catatan tes.',
                ],
            ],
        ]);
        $responseInactive->assertSessionHasErrors('employee_id');

        // ID tidak valid
        $responseInvalid = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'employee',
            'input_mode' => 'select',
            'employee_id' => 999999,
            'categories' => [
                [
                    'category' => 'kedisiplinan_karyawan',
                    'notes' => 'Catatan tes.',
                ],
            ],
        ]);
        $responseInvalid->assertSessionHasErrors('employee_id');
    }

    /**
     * 7. Monitoring kantor dapat menyimpan beberapa kategori dan tindakan.
     */
    public function test_office_monitoring_stores_multiple_categories_and_actions(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $response = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'office',
            'office_items' => [
                [
                    'category' => 'fasilitas_peralatan',
                    'finding' => 'Printer kasir macet.',
                    'action' => 'melaporkan_kerusakan',
                    'action_notes' => 'Hubungi vendor servis teknis.',
                ],
                [
                    'category' => 'keamanan_kenyamanan_k3',
                    'finding' => 'Jalur evakuasi terhalang kardus.',
                    'action' => 'mengingatkan_karyawan',
                    'action_notes' => 'Kardus telah dipindahkan.',
                ],
            ],
        ]);

        $report = HrdDailyReport::where('report_type', 'office')->first();
        $this->assertNotNull($report);
        $this->assertCount(2, $report->monitoring_data);
        $this->assertEquals('melaporkan_kerusakan', $report->monitoring_data[0]['action']);
        $this->assertNull($report->employee_id);
        $this->assertNull($report->employee_name);
    }

    /**
     * 8. Kategori duplikat serta payload tidak valid ditolak.
     */
    public function test_duplicate_categories_and_invalid_payloads_are_rejected(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        // Kategori karyawan duplikat
        $responseEmployeeDuplicate = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'employee',
            'input_mode' => 'manual',
            'manual_employee_name' => 'Tes Duplikat',
            'categories' => [
                [
                    'category' => 'kedisiplinan_karyawan',
                    'notes' => 'Catatan 1.',
                ],
                [
                    'category' => 'kedisiplinan_karyawan',
                    'notes' => 'Catatan 2.',
                ],
            ],
        ]);
        $responseEmployeeDuplicate->assertSessionHasErrors('categories.1.category');

        // Kategori kantor duplikat
        $responseOfficeDuplicate = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'office',
            'office_items' => [
                [
                    'category' => 'kebersihan_kerapian',
                    'finding' => 'Temuan 1.',
                    'action' => 'tidak_perlu_tindakan',
                    'action_notes' => 'Tindakan 1.',
                ],
                [
                    'category' => 'kebersihan_kerapian',
                    'finding' => 'Temuan 2.',
                    'action' => 'tidak_perlu_tindakan',
                    'action_notes' => 'Tindakan 2.',
                ],
            ],
        ]);
        $responseOfficeDuplicate->assertSessionHasErrors('office_items.1.category');

        // Tindakan tidak valid
        $responseInvalidAction = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'office',
            'office_items' => [
                [
                    'category' => 'fasilitas_peralatan',
                    'finding' => 'Temuan.',
                    'action' => 'aksi_fiktif',
                    'action_notes' => 'Catatan.',
                ],
            ],
        ]);
        $responseInvalidAction->assertSessionHasErrors('office_items.0.action');
    }

    /**
     * 9. Jenis laporan employee dan office menyimpan data sesuai jenisnya.
     */
    public function test_payload_isolation_between_employee_and_office(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        // Simpan office tapi coba selipkan data employee
        $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'office',
            'employee_id' => $this->activeEmployee->id,
            'manual_employee_name' => 'Nama Yang Harusnya Dihapus',
            'office_items' => [
                [
                    'category' => 'kebersihan_kerapian',
                    'finding' => 'Lantai lobi bersih.',
                    'action' => 'tidak_perlu_tindakan',
                    'action_notes' => 'Bagus.',
                ],
            ],
        ]);

        $officeReport = HrdDailyReport::where('report_type', 'office')->first();
        $this->assertNull($officeReport->employee_id);
        $this->assertNull($officeReport->employee_name);
    }

    /**
     * 10. Nama pembuat tetap tersedia pada arsip setelah akun pembuat dihapus (ON DELETE SET NULL).
     */
    public function test_creator_snapshot_persists_when_user_account_is_deleted(): void
    {
        $tempHrd = AdminHrdUser::create([
            'name' => 'HRD Sementara',
            'email' => 'temp_hrd@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
            'is_active' => true,
        ]);

        $this->actingAs($tempHrd, 'admin_hrd');

        $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-27',
            'report_type' => 'employee',
            'input_mode' => 'manual',
            'manual_employee_name' => 'Dina Akuntansi',
            'categories' => [
                [
                    'category' => 'kepatuhan_aturan',
                    'notes' => 'Tertib mengenakan seragam kerja.',
                ],
            ],
        ]);

        $report = HrdDailyReport::where('employee_name', 'Dina Akuntansi')->first();
        $this->assertEquals($tempHrd->id, $report->admin_hrd_user_id);
        $this->assertEquals('HRD Sementara', $report->creator_name_snapshot);

        // Hapus akun pembuat
        $tempHrd->delete();

        $freshReport = $report->fresh();
        $this->assertNull($freshReport->admin_hrd_user_id);
        $this->assertEquals('HRD Sementara', $freshReport->creator_name_snapshot);
    }

    /**
     * 11. Halaman daftar dan detail berhasil dirender.
     */
    public function test_index_and_show_pages_render_successfully(): void
    {
        $this->actingAs($this->admin, 'admin_hrd');

        $report = HrdDailyReport::create([
            'admin_hrd_user_id' => $this->admin->id,
            'creator_name_snapshot' => $this->admin->name,
            'report_date' => '2026-10-27',
            'report_type' => 'employee',
            'employee_id' => $this->activeEmployee->id,
            'employee_name' => $this->activeEmployee->name,
            'monitoring_data' => [
                [
                    'category' => 'kerja_sama',
                    'notes' => 'Membantu tim logistik menyelesaikan packing inventaris.',
                ],
            ],
        ]);

        $indexRes = $this->get(route('admin.hrd-reports.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Budi Santoso');
        $indexRes->assertSee('Monitoring Karyawan');

        $showRes = $this->get(route('admin.hrd-reports.show', $report));
        $showRes->assertStatus(200);
        $showRes->assertSee('Kerja Sama Antarkaryawan');
        $showRes->assertSee('Membantu tim logistik menyelesaikan packing inventaris.');
    }
}
