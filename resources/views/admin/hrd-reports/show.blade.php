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
                            @if ($hrdReport->report_type === 'employee')
                                Monitoring Karyawan
                            @else
                                Monitoring Kondisi Kantor
                            @endif
                        </h3>
                    </div>

                    <div>
                        @if ($hrdReport->report_type === 'employee')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                Karyawan: {{ $hrdReport->employee_name }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
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
                        <span class="text-slate-400 block font-semibold">Target Subjek</span>
                        <span class="font-medium text-slate-700 text-sm">
                            {{ $hrdReport->report_type === 'employee' ? $hrdReport->employee_name : 'Kondisi Kantor' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold">Waktu Pembuatan</span>
                        <span class="font-medium text-slate-700 text-sm">{{ $hrdReport->created_at->format('H:i, d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Konten Monitoring -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h4 class="text-base font-bold text-slate-900">
                        @if ($hrdReport->report_type === 'employee')
                            Hasil Monitoring Karyawan ({{ $hrdReport->employee_name }})
                        @else
                            Hasil Monitoring Kondisi Kantor
                        @endif
                    </h4>
                    <span class="text-xs text-slate-400">
                        Total Item: {{ is_array($hrdReport->monitoring_data) ? count($hrdReport->monitoring_data) : 0 }}
                    </span>
                </div>

                @php
                    $items = is_array($hrdReport->monitoring_data) ? $hrdReport->monitoring_data : [];

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
                @endphp

                <!-- Tampilan Monitoring Karyawan -->
                @if ($hrdReport->report_type === 'employee')
                    <div class="space-y-4">
                        @forelse ($items as $item)
                            @php
                                $catKey = $item['category'] ?? '';
                                $label = $employeeCategoryLabels[$catKey] ?? ucwords(str_replace('_', ' ', $catKey));
                                $notes = $item['notes'] ?? '-';
                            @endphp
                            <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <h5 class="text-sm font-bold text-slate-900">{{ $label }}</h5>
                                </div>
                                <div class="text-xs text-slate-700 pl-4 whitespace-pre-line leading-relaxed">
                                    {{ $notes }}
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Tidak ada catatan monitoring.</p>
                        @endforelse
                    </div>
                @endif

                <!-- Tampilan Monitoring Kondisi Kantor -->
                @if ($hrdReport->report_type === 'office')
                    <div class="space-y-5">
                        @forelse ($items as $item)
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
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
