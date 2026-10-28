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

    private Employee $activeEmployee2;

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

        $this->activeEmployee2 = Employee::create([
            'division_id' => $this->division->id,
            'name' => 'Siti Aminah',
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
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee->id,
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => 'selesai',
                            'notes' => 'Datang tepat waktu dan memimpin briefing.',
                        ],
                    ],
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
        $showRes->assertSee('Selesai');
    }

    /**
     * 3. Admin dapat membuka halaman, menyimpan laporan kantor, dan melihat detail.
     */
    public function test_admin_can_view_index_create_report_and_view_show(): void
    {
        $this->actingAs($this->admin, 'admin_hrd');

        $indexRes = $this->get(route('admin.hrd-reports.index'));
        $indexRes->assertStatus(200);

        $payload = [
            'report_date' => '2026-10-28',
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
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee->id,
                    'categories' => [
                        [
                            'category' => 'sikap_perilaku',
                            'follow_up_status' => 'dalam_proses',
                            'notes' => 'Sangat kooperatif dengan rekan kerja.',
                        ],
                    ],
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
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'manual',
                    'manual_employee_name' => 'Rian Magang IT',
                    'categories' => [
                        [
                            'category' => 'aktivitas_tanggung_jawab',
                            'follow_up_status' => 'menunggu_pihak_terkait',
                            'notes' => 'Menyelesaikan modul pengujian dasar.',
                        ],
                    ],
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
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->inactiveEmployee->id,
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => 'selesai',
                            'notes' => 'Catatan tes.',
                        ],
                    ],
                ],
            ],
        ]);
        $responseInactive->assertSessionHasErrors('employees.0.employee_id');

        // ID tidak valid
        $responseInvalid = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => 999999,
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => 'selesai',
                            'notes' => 'Catatan tes.',
                        ],
                    ],
                ],
            ],
        ]);
        $responseInvalid->assertSessionHasErrors('employees.0.employee_id');
    }

    /**
     * 7. Satu laporan dapat menyimpan beberapa karyawan dan setiap kategori memiliki status tindak lanjut masing-masing.
     */
    public function test_multiple_employees_with_independent_status_per_category(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $payload = [
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee->id,
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => 'dalam_proses',
                            'notes' => 'Terlambat 10 menit.',
                        ],
                        [
                            'category' => 'komunikasi_koordinasi',
                            'follow_up_status' => 'selesai',
                            'notes' => 'Briefing internal berjalan lancar.',
                        ],
                    ],
                ],
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee2->id,
                    'categories' => [
                        [
                            'category' => 'sikap_perilaku',
                            'follow_up_status' => 'perlu_monitoring_lanjutan',
                            'notes' => 'Perlu bimbingan interaksi dengan klien.',
                        ],
                    ],
                ],
                [
                    'input_mode' => 'manual',
                    'manual_employee_name' => 'Ahmad Teknisi Luar',
                    'categories' => [
                        [
                            'category' => 'hal_positif_apresiasi',
                            'follow_up_status' => 'memerlukan_keputusan_direktur',
                            'notes' => 'Proposal bonus penugasan khusus.',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->post(route('admin.hrd-reports.store'), $payload);
        $report = HrdDailyReport::latest('id')->first();
        $this->assertNotNull($report);
        $response->assertRedirect(route('admin.hrd-reports.show', $report));

        // Data 3 karyawan tersimpan
        $this->assertCount(3, $report->monitoring_data);
        
        // Karyawan 1 memiliki 2 kategori dengan status berbeda
        $emp1Categories = $report->monitoring_data[0]['categories'];
        $this->assertCount(2, $emp1Categories);
        $this->assertEquals('kedisiplinan_karyawan', $emp1Categories[0]['category']);
        $this->assertEquals('dalam_proses', $emp1Categories[0]['follow_up_status']);
        $this->assertEquals('Terlambat 10 menit.', $emp1Categories[0]['notes']);

        $this->assertEquals('komunikasi_koordinasi', $emp1Categories[1]['category']);
        $this->assertEquals('selesai', $emp1Categories[1]['follow_up_status']);
        $this->assertEquals('Briefing internal berjalan lancar.', $emp1Categories[1]['notes']);

        // Karyawan 2
        $emp2Categories = $report->monitoring_data[1]['categories'];
        $this->assertCount(1, $emp2Categories);
        $this->assertEquals('perlu_monitoring_lanjutan', $emp2Categories[0]['follow_up_status']);

        // Karyawan 3
        $emp3Categories = $report->monitoring_data[2]['categories'];
        $this->assertCount(1, $emp3Categories);
        $this->assertEquals('memerlukan_keputusan_direktur', $emp3Categories[0]['follow_up_status']);
    }

    /**
     * 8. Kelima status tindak lanjut HRD valid di dalam kategori.
     */
    public function test_all_five_follow_up_statuses_are_valid_in_category(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $statuses = [
            'selesai',
            'dalam_proses',
            'menunggu_pihak_terkait',
            'perlu_monitoring_lanjutan',
            'memerlukan_keputusan_direktur',
        ];

        foreach ($statuses as $status) {
            $response = $this->post(route('admin.hrd-reports.store'), [
                'report_date' => '2026-10-28',
                'report_type' => 'employee',
                'employees' => [
                    [
                        'input_mode' => 'manual',
                        'manual_employee_name' => 'Staf Status ' . $status,
                        'categories' => [
                            [
                                'category' => 'sikap_perilaku',
                                'follow_up_status' => $status,
                                'notes' => 'Catatan status ' . $status,
                            ],
                        ],
                    ],
                ],
            ]);

            $response->assertSessionHasNoErrors();
        }

        $this->assertEquals(5, HrdDailyReport::count());
    }

    /**
     * 9. Status tindak lanjut tidak valid di dalam kategori ditolak.
     */
    public function test_invalid_follow_up_status_in_category_is_rejected(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $response = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee->id,
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => 'status_palsu',
                            'notes' => 'Catatan.',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertSessionHasErrors('employees.0.categories.0.follow_up_status');
    }

    /**
     * 10. Status tindak lanjut kosong di dalam kategori ditolak.
     */
    public function test_missing_follow_up_status_in_category_is_rejected(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $response = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee->id,
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => '',
                            'notes' => 'Catatan.',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertSessionHasErrors('employees.0.categories.0.follow_up_status');
    }

    /**
     * 11. Kategori duplikat pada karyawan yang sama ditolak.
     */
    public function test_duplicate_categories_for_same_employee_are_rejected(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $response = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee->id,
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => 'selesai',
                            'notes' => 'Catatan 1.',
                        ],
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => 'dalam_proses',
                            'notes' => 'Catatan 2.',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertSessionHasErrors('employees.0.categories.1.category');
    }

    /**
     * 12. Karyawan yang sama diduplikasi dalam satu laporan ditolak.
     */
    public function test_duplicate_employee_id_in_same_report_is_rejected(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $response = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee->id,
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => 'selesai',
                            'notes' => 'Catatan 1.',
                        ],
                    ],
                ],
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee->id,
                    'categories' => [
                        [
                            'category' => 'sikap_perilaku',
                            'follow_up_status' => 'dalam_proses',
                            'notes' => 'Catatan 2.',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertSessionHasErrors('employees.1.employee_id');
    }

    /**
     * 13. Monitoring kantor dapat menyimpan beberapa kategori dan tindakan.
     */
    public function test_office_monitoring_stores_multiple_categories_and_actions(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        $response = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-28',
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
     * 14. Kategori kantor duplikat serta aksi tidak valid ditolak.
     */
    public function test_duplicate_office_categories_and_invalid_action_rejected(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        // Kategori kantor duplikat
        $responseOfficeDuplicate = $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-28',
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
            'report_date' => '2026-10-28',
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
     * 15. Isolasi payload antara monitoring karyawan dan kantor.
     */
    public function test_payload_isolation_between_employee_and_office(): void
    {
        $this->actingAs($this->hrd, 'admin_hrd');

        // Simpan office tapi coba selipkan data employee
        $this->post(route('admin.hrd-reports.store'), [
            'report_date' => '2026-10-28',
            'report_type' => 'office',
            'employees' => [
                [
                    'input_mode' => 'select',
                    'employee_id' => $this->activeEmployee->id,
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'follow_up_status' => 'selesai',
                            'notes' => 'catatan',
                        ],
                    ],
                ],
            ],
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
     * 16. Nama pembuat tetap tersedia pada arsip setelah akun pembuat dihapus (ON DELETE SET NULL).
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
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employees' => [
                [
                    'input_mode' => 'manual',
                    'manual_employee_name' => 'Dina Akuntansi',
                    'categories' => [
                        [
                            'category' => 'kepatuhan_aturan',
                            'follow_up_status' => 'selesai',
                            'notes' => 'Tertib mengenakan seragam kerja.',
                        ],
                    ],
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
     * 17. Halaman daftar dan detail berhasil dirender, termasuk backward-compatibility untuk laporan lama.
     */
    public function test_index_and_show_pages_render_successfully_including_legacy_reports(): void
    {
        $this->actingAs($this->admin, 'admin_hrd');

        // Laporan format baru: status per kategori
        $newReport = HrdDailyReport::create([
            'admin_hrd_user_id' => $this->admin->id,
            'creator_name_snapshot' => $this->admin->name,
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employee_id' => $this->activeEmployee->id,
            'employee_name' => 'Budi Santoso, Siti Aminah',
            'monitoring_data' => [
                [
                    'employee_id' => $this->activeEmployee->id,
                    'employee_name' => 'Budi Santoso',
                    'input_mode' => 'select',
                    'categories' => [
                        [
                            'category' => 'kerja_sama',
                            'follow_up_status' => 'selesai',
                            'notes' => 'Membantu tim logistik menyelesaikan packing.',
                        ],
                    ],
                ],
                [
                    'employee_id' => $this->activeEmployee2->id,
                    'employee_name' => 'Siti Aminah',
                    'input_mode' => 'select',
                    'categories' => [
                        [
                            'category' => 'komunikasi_koordinasi',
                            'follow_up_status' => 'dalam_proses',
                            'notes' => 'Follow up klien berjalan.',
                        ],
                    ],
                ],
            ],
        ]);

        // Laporan format versi sebelumnya (status di level karyawan)
        $previousVersionReport = HrdDailyReport::create([
            'admin_hrd_user_id' => $this->admin->id,
            'creator_name_snapshot' => $this->admin->name,
            'report_date' => '2026-10-28',
            'report_type' => 'employee',
            'employee_id' => $this->activeEmployee->id,
            'employee_name' => 'Budi Santoso',
            'monitoring_data' => [
                [
                    'employee_id' => $this->activeEmployee->id,
                    'employee_name' => 'Budi Santoso',
                    'input_mode' => 'select',
                    'follow_up_status' => 'menunggu_pihak_terkait',
                    'categories' => [
                        [
                            'category' => 'kedisiplinan_karyawan',
                            'notes' => 'Catatan format perantara.',
                        ],
                    ],
                ],
            ],
        ]);

        // Laporan format lama (single-employee flat legacy format)
        $legacyReport = HrdDailyReport::create([
            'admin_hrd_user_id' => $this->admin->id,
            'creator_name_snapshot' => $this->admin->name,
            'report_date' => '2026-10-27',
            'report_type' => 'employee',
            'employee_id' => $this->activeEmployee->id,
            'employee_name' => 'Budi Santoso',
            'monitoring_data' => [
                [
                    'category' => 'sikap_perilaku',
                    'notes' => 'Catatan legacy report format lama.',
                ],
            ],
        ]);

        $indexRes = $this->get(route('admin.hrd-reports.index'));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Budi Santoso, Siti Aminah');
        $indexRes->assertSee('2 Karyawan');

        // Detail laporan baru (status per kategori)
        $showNewRes = $this->get(route('admin.hrd-reports.show', $newReport));
        $showNewRes->assertStatus(200);
        $showNewRes->assertSee('Budi Santoso');
        $showNewRes->assertSee('Siti Aminah');
        $showNewRes->assertSee('Selesai');
        $showNewRes->assertSee('Dalam proses');
        $showNewRes->assertSee('Membantu tim logistik menyelesaikan packing.');

        // Detail laporan versi sebelumnya
        $showPrevRes = $this->get(route('admin.hrd-reports.show', $previousVersionReport));
        $showPrevRes->assertStatus(200);
        $showPrevRes->assertSee('Menunggu pihak terkait');
        $showPrevRes->assertSee('Catatan format perantara.');

        // Detail laporan legacy format flat
        $showLegacyRes = $this->get(route('admin.hrd-reports.show', $legacyReport));
        $showLegacyRes->assertStatus(200);
        $showLegacyRes->assertSee('Budi Santoso');
        $showLegacyRes->assertSee('Catatan legacy report format lama.');
    }
}
