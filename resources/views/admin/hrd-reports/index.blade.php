<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Riwayat Daily Report HRD') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh laporan monitoring yang dibuat oleh tim HRD.</p>
            </div>

            <a href="{{ route('admin.hrd-reports.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold shadow hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Laporan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Table of HRD Reports -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-6">Tanggal Laporan</th>
                                <th class="py-3.5 px-6">Jenis Monitoring</th>
                                <th class="py-3.5 px-6">Target / Subjek</th>
                                <th class="py-3.5 px-6">Pembuat</th>
                                <th class="py-3.5 px-6">Waktu Dibuat</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($reports as $report)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-6 font-semibold text-slate-900">
                                        {{ $report->report_date->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="py-3.5 px-6">
                                        @if ($report->report_type === 'both')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                                Karyawan &amp; Kantor
                                            </span>
                                        @elseif ($report->report_type === 'employee')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                                                Monitoring Karyawan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Kondisi Kantor
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6">
                                        @if ($report->report_type === 'both')
                                            <div>
                                                <span class="font-medium text-slate-900">{{ $report->employee_name }}</span>
                                                @php
                                                    $empList = $report->getEmployeesData();
                                                    $count = count($empList);
                                                @endphp
                                                @if ($count > 1)
                                                    <span class="inline-block ml-1 text-[10px] font-bold px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700">
                                                        {{ $count }} Karyawan
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="mt-0.5">
                                                <span class="inline-block text-[10px] font-semibold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    + Kondisi Kantor
                                                </span>
                                            </div>
                                        @elseif ($report->report_type === 'employee')
                                            <div class="font-medium text-slate-900">{{ $report->employee_name }}</div>
                                            @php
                                                $empList = $report->getEmployeesData();
                                                $count = count($empList);
                                            @endphp
                                            @if ($count > 1)
                                                <span class="inline-block mt-0.5 text-[10px] font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700">
                                                    {{ $count }} Karyawan
                                                </span>
                                            @endif
                                        @else
                                            <div class="text-slate-500 italic text-xs">Monitoring Kantor</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <div class="font-medium text-slate-900">{{ $report->creator_name_snapshot }}</div>
                                    </td>
                                    <td class="py-3.5 px-6 text-xs text-slate-500">
                                        {{ $report->created_at->format('H:i, d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right">
                                        <a href="{{ route('admin.hrd-reports.show', $report) }}"
                                           class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-xs text-slate-400">
                                        Belum ada laporan Daily Report HRD.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($reports->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
