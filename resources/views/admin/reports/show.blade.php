<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Detail Laporan Harian') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Rincian laporan pekerjaan oleh {{ $report->employee_name_snapshot }}.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                    &larr; Kembali
                </a>
                <a href="{{ route('admin.reports.edit', $report) }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold shadow hover:bg-indigo-700 transition">
                    Edit Laporan
                </a>
            </div>
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
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Identitas Laporan</span>
                        <h3 class="text-xl font-bold text-slate-900">{{ $report->employee_name_snapshot }}</h3>
                    </div>

                    <div>
                        @if ($report->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                Status: Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                Status: Dibatalkan
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold">Divisi / Posisi</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $report->division_name_snapshot }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold">Tanggal Pekerjaan</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $report->report_date->translatedFormat('d F Y') }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold">Email Pengirim</span>
                        <span class="font-medium text-slate-700">{{ $report->email }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold">Waktu Submit</span>
                        <span class="font-medium text-slate-700">{{ $report->submitted_at->format('H:i:s, d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Form Data Content Card (Rendered based on division) -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h4 class="text-base font-bold text-slate-900">Rincian Laporan Form ({{ $report->division_name_snapshot }})</h4>
                    <span class="text-xs text-slate-400">Versi Form: v{{ $report->form_version }}</span>
                </div>

                @php
                    $data = (array) $report->form_data;
                    $code = $report->division_code_snapshot;
                @endphp

                <!-- TEKNISI -->
                @if ($code === 'teknisi')
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Pekerjaan Hari Ini:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach ((array)($data['pekerjaan_hari_ini'] ?? []) as $job)
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-xs border border-indigo-100">
                                        {{ ucfirst(str_replace('_', ' ', $job)) }}
                                    </span>
                                @endforeach
                            </div>
                            @if (!empty($data['pekerjaan_lainnya']))
                                <p class="mt-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                    <strong>Penjelasan Pekerjaan Lainnya:</strong> {{ $data['pekerjaan_lainnya'] }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status Pekerjaan:</span>
                            <span class="font-bold text-slate-800 capitalize">{{ $data['status_pekerjaan'] ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kendala / Hambatan:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['kendala'] ?: 'Tidak ada kendala.' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Rencana Besok:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['rencana_besok'] ?: '-' }}</p>
                        </div>
                    </div>

                <!-- ADMIN SALES -->
                @elseif ($code === 'admin_sales')
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Hari Ini:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach ((array)($data['pekerjaan_hari_ini'] ?? []) as $job)
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-xs border border-indigo-100">
                                        {{ ucfirst(str_replace('_', ' ', $job)) }}
                                    </span>
                                @endforeach
                            </div>
                            @if (!empty($data['pekerjaan_hari_ini_lainnya']))
                                <p class="mt-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                    <strong>Penjelasan:</strong> {{ $data['pekerjaan_hari_ini_lainnya'] }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Rencana Pekerjaan Besok:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach ((array)($data['rencana_besok'] ?? []) as $job)
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200">
                                        {{ ucfirst(str_replace('_', ' ', $job)) }}
                                    </span>
                                @endforeach
                            </div>
                            @if (!empty($data['rencana_besok_lainnya']))
                                <p class="mt-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                    <strong>Penjelasan Rencana:</strong> {{ $data['rencana_besok_lainnya'] }}
                                </p>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 gap-4 pt-2">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-center">
                                <span class="text-xs text-slate-500 block">Follow Up Customer</span>
                                <span class="text-xl font-extrabold text-slate-900">{{ $data['jumlah_customer'] ?? 0 }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-center">
                                <span class="text-xs text-slate-500 block">Quotation Dibuat</span>
                                <span class="text-xl font-extrabold text-slate-900">{{ $data['jumlah_quotation'] ?? 0 }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-center">
                                <span class="text-xs text-slate-500 block">Closing Hari Ini</span>
                                <span class="text-xl font-extrabold text-emerald-600">{{ $data['jumlah_closing'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kendala:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['kendala'] ?: 'Tidak ada kendala.' }}</p>
                        </div>
                    </div>

                <!-- ADMIN PROJECT -->
                @elseif ($code === 'admin_project')
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Hari Ini:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['pekerjaan_hari_ini'] ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Project:</span>
                                <p class="font-bold text-slate-800">{{ $data['nama_project'] ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Progres Project:</span>
                                <div class="flex items-center gap-3">
                                    <span class="font-extrabold text-indigo-600">{{ $data['progres_persen'] ?? 0 }}%</span>
                                    <div class="flex-1 bg-slate-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $data['progres_persen'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Dokumen yang Diproses:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach ((array)($data['dokumen_diproses'] ?? []) as $doc)
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-xs border border-indigo-100">
                                        {{ strtoupper(str_replace('_', ' ', $doc)) }}
                                    </span>
                                @endforeach
                            </div>
                            @if (!empty($data['dokumen_lainnya']))
                                <p class="mt-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                    <strong>Dokumen Lainnya:</strong> {{ $data['dokumen_lainnya'] }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kendala:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['kendala'] ?: 'Tidak ada kendala.' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Rencana Besok:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['rencana_besok'] ?: '-' }}</p>
                        </div>
                    </div>

                <!-- ADMIN PROCUREMENT -->
                @elseif ($code === 'admin_procurement')
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Hari Ini:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['pekerjaan_hari_ini'] ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Jumlah PO Dibuat:</span>
                                <span class="font-extrabold text-slate-900 text-lg">{{ $data['jumlah_po'] ?? 0 }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Vendor yang Dihubungi:</span>
                                <p class="font-medium text-slate-800">{{ $data['vendor_dihubungi'] ?: '-' }}</p>
                            </div>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Barang yang Diterima atau Dikirim:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['barang_diterima_dikirim'] ?: '-' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kendala:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['kendala'] ?: 'Tidak ada kendala.' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Rencana Besok:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['rencana_besok'] ?: '-' }}</p>
                        </div>
                    </div>

                <!-- SYSTEM INFORMASI -->
                @elseif ($code === 'system_informasi')
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Hari Ini:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['pekerjaan_hari_ini'] ?? '-' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status Pengerjaan:</span>
                            <p class="font-bold text-slate-800">{{ $data['status_pengerjaan'] ?? '-' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kendala:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['kendala'] ?: 'Tidak ada kendala.' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Rencana Besok:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['rencana_besok'] ?: '-' }}</p>
                        </div>
                    </div>

                <!-- FINANCE -->
                @elseif ($code === 'finance')
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pekerjaan Hari Ini:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['pekerjaan_hari_ini'] ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Invoice yang Dibuat:</span>
                                <p class="font-medium text-slate-800">{{ $data['invoice_dibuat'] ?: '-' }}</p>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pembayaran / Penerimaan:</span>
                                <p class="font-medium text-slate-800">{{ $data['pembayaran'] ?: '-' }}</p>
                            </div>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Rekap Kas atau Bank:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['rekap_kas_bank'] ?: '-' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Kendala:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['kendala'] ?: 'Tidak ada kendala.' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Rencana Pekerjaan Besok:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 whitespace-pre-line">{{ $data['rencana_besok'] ?: '-' }}</p>
                        </div>
                    </div>

                <!-- GENERIC FALLBACK -->
                @else
                    <div class="space-y-3">
                        @foreach ($data as $key => $val)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                <p class="mt-1 text-sm text-slate-800">{{ is_array($val) ? implode(', ', $val) : $val }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Action Buttons Footer -->
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800">
                    &larr; Kembali ke Daftar Laporan
                </a>

                <div class="flex items-center gap-2">
                    @if ($report->status === 'active')
                        <form method="POST" action="{{ route('admin.reports.cancel', $report) }}"
                              onsubmit="return confirm('Apakah Anda yakin ingin membatalkan laporan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-semibold transition">
                                Batalkan Laporan Ini
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.reports.restore', $report) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition">
                                Pulihkan Laporan Ini
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
