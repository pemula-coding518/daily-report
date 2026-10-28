<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Detail Daily Report HRD') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Rincian laporan monitoring yang dibuat oleh {{ $hrdReport->creator_name_snapshot }}.</p>
            </div>

            <a href="{{ route('admin.hrd-reports.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1. Header Information Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Jenis Laporan</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-0.5">
                            @if ($hrdReport->report_type === 'both')
                                Monitoring Karyawan &amp; Kondisi Kantor
                            @elseif ($hrdReport->report_type === 'employee')
                                Monitoring Karyawan
                            @else
                                Monitoring Kondisi Kantor
                            @endif
                        </h3>
                    </div>

                    <div>
                        @if ($hrdReport->report_type === 'both')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                Monitoring Karyawan & Kantor
                            </span>
                        @elseif ($hrdReport->report_type === 'employee')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                Monitoring Karyawan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                Area Kantor
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold">Tanggal Laporan</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $hrdReport->report_date->translatedFormat('d F Y') }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold">Pembuat Laporan</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $hrdReport->creator_name_snapshot }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold">Subjek Laporan</span>
                        <span class="font-medium text-slate-700 text-sm">
                            @if ($hrdReport->report_type === 'both')
                                {{ $hrdReport->employee_name }} &amp; Kondisi Kantor
                            @elseif ($hrdReport->report_type === 'employee')
                                {{ $hrdReport->employee_name }}
                            @else
                                Kondisi Kantor
                            @endif
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold">Waktu Pembuatan</span>
                        <span class="font-medium text-slate-700 text-sm">{{ $hrdReport->created_at->format('H:i, d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            @php
                $employeeCategoryLabels = [
                    'kedisiplinan_karyawan' => 'Kedisiplinan Karyawan',
                    'sikap_perilaku' => 'Sikap dan Perilaku',
                    'aktivitas_tanggung_jawab' => 'Aktivitas dan Tanggung Jawab Pekerjaan',
                    'komunikasi_koordinasi' => 'Komunikasi dan Koordinasi',
                    'kepatuhan_aturan' => 'Kepatuhan terhadap Aturan Perusahaan',
                    'kerja_sama' => 'Kerja Sama Antarkaryawan',
                    'kendala_permasalahan' => 'Kendala / Permasalahan Karyawan',
                    'hal_positif_apresiasi' => 'Hal Positif / Apresiasi Karyawan',
                    'temuan_lainnya' => 'Temuan Lainnya',
                ];

                $officeCategoryLabels = [
                    'kebersihan_kerapian' => 'Kebersihan dan Kerapian',
                    'fasilitas_peralatan' => 'Fasilitas dan Peralatan',
                    'keamanan_kenyamanan_k3' => 'Keamanan, Kenyamanan / Keselamatan Kerja',
                ];

                $actionLabels = [
                    'tidak_perlu_tindakan' => 'Tidak perlu tindakan',
                    'mengingatkan_karyawan' => 'Mengingatkan karyawan',
                    'koordinasi_atasan' => 'Koordinasi dengan atasan',
                    'melaporkan_kerusakan' => 'Melaporkan kerusakan',
                    'mengajukan_perbaikan' => 'Mengajukan perbaikan',
                ];

                $actionColors = [
                    'tidak_perlu_tindakan' => 'bg-slate-100 text-slate-700',
                    'mengingatkan_karyawan' => 'bg-amber-100 text-amber-800',
                    'koordinasi_atasan' => 'bg-indigo-100 text-indigo-800',
                    'melaporkan_kerusakan' => 'bg-rose-100 text-rose-800',
                    'mengajukan_perbaikan' => 'bg-blue-100 text-blue-800',
                ];

                $employeeList = $hrdReport->getEmployeesData();
                $officeItems = $hrdReport->getOfficeData();
            @endphp

            <!-- 2. KONTEN MONITORING KARYAWAN -->
            @if ($hrdReport->hasEmployeeMonitoring() && count($employeeList) > 0)
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                            <h4 class="text-base font-bold text-slate-900">
                                Hasil Monitoring Karyawan
                            </h4>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700">
                            Total: {{ count($employeeList) }} Karyawan
                        </span>
                    </div>

                    <div class="space-y-6">
                        @forelse ($employeeList as $empIdx => $empEntry)
                            @php
                                $empName = $empEntry['employee_name'] ?? 'Karyawan #' . ($empIdx + 1);
                                $empCategories = $empEntry['categories'] ?? [];
                                $fallbackStatus = $empEntry['follow_up_status'] ?? null;
                            @endphp

                            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                                <!-- Header Entri Karyawan -->
                                <div class="p-4 sm:p-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                                            {{ $empIdx + 1 }}
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Karyawan</span>
                                            <h5 class="text-base font-bold text-slate-900 leading-tight">{{ $empName }}</h5>
                                        </div>
                                    </div>

                                    @if ($fallbackStatus)
                                        <!-- Fallback badge untuk laporan versi sebelumnya -->
                                        <div>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ \App\Models\HrdDailyReport::getFollowUpStatusBadgeColor($fallbackStatus) }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                Status: {{ \App\Models\HrdDailyReport::getFollowUpStatusLabel($fallbackStatus) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Detail Kategori Monitoring untuk Karyawan ini -->
                                <div class="p-5 sm:p-6 space-y-4">
                                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Kategori, Status Tindak Lanjut, &amp; Detail Catatan:</span>
                                    
                                    <div class="space-y-4">
                                        @forelse ($empCategories as $catItem)
                                            @php
                                                $catKey = $catItem['category'] ?? '';
                                                $label = $employeeCategoryLabels[$catKey] ?? ucwords(str_replace('_', ' ', $catKey));
                                                $notes = $catItem['notes'] ?? '-';
                                                $catStatus = $catItem['follow_up_status'] ?? $fallbackStatus;
                                                $catStatusLabel = \App\Models\HrdDailyReport::getFollowUpStatusLabel($catStatus);
                                                $catStatusBadge = \App\Models\HrdDailyReport::getFollowUpStatusBadgeColor($catStatus);
                                            @endphp
                                            <div class="p-4 sm:p-5 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3">
                                                <!-- Baris Header Kategori: Judul Kategori & Badge Status Tindak Lanjut HRD -->
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-200/70">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                                        <h6 class="text-sm font-bold text-slate-900">{{ $label }}</h6>
                                                    </div>

                                                    @if ($catStatus)
                                                        <div>
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $catStatusBadge }}">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                                Tindak Lanjut: {{ $catStatusLabel }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Kolom Detail Catatan Manual -->
                                                <div class="space-y-1">
                                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Detail Catatan:</span>
                                                    <div class="text-xs text-slate-800 pl-2 whitespace-pre-line leading-relaxed">
                                                        {{ $notes }}
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-xs text-slate-400 italic">Tidak ada kategori yang dicatat.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Tidak ada catatan monitoring karyawan.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- 3. KONTEN MONITORING KONDISI KANTOR -->
            @if ($hrdReport->hasOfficeMonitoring() && count($officeItems) > 0)
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                            <h4 class="text-base font-bold text-slate-900">
                                Hasil Monitoring Kondisi Kantor
                            </h4>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700">
                            Total: {{ count($officeItems) }} Temuan
                        </span>
                    </div>

                    <div class="space-y-5">
                        @forelse ($officeItems as $item)
                            @php
                                $catKey = $item['category'] ?? '';
                                $label = $officeCategoryLabels[$catKey] ?? ucwords(str_replace('_', ' ', $catKey));
                                $finding = $item['finding'] ?? '-';
                                $actionKey = $item['action'] ?? '';
                                $actionLabel = $actionLabels[$actionKey] ?? ucwords(str_replace('_', ' ', $actionKey));
                                $actionColor = $actionColors[$actionKey] ?? 'bg-slate-100 text-slate-700';
                                $actionNotes = $item['action_notes'] ?? '-';
                            @endphp
                            <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        <h5 class="text-sm font-bold text-slate-900">{{ $label }}</h5>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $actionColor }}">
                                            Tindakan: {{ $actionLabel }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                    <div class="space-y-1">
                                        <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Detail Temuan:</span>
                                        <p class="text-slate-800 whitespace-pre-line leading-relaxed">{{ $finding }}</p>
                                    </div>

                                    <div class="space-y-1">
                                        <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Catatan Tindakan:</span>
                                        <p class="text-slate-800 whitespace-pre-line leading-relaxed">{{ $actionNotes }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Tidak ada catatan kondisi kantor.</p>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
