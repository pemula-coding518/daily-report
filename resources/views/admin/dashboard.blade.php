<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Dashboard Monitoring Daily Report') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pantau tingkat kepatuhan dan status pengiriman laporan harian karyawan.</p>
            </div>

            <!-- Date & Division Filter Form (Primary Dashboard Filter) -->
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2">
                <div>
                    <input type="date" name="date" value="{{ $selectedDate }}" 
                           onchange="this.form.submit()"
                           class="text-xs font-semibold rounded-xl border-slate-300 text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <select name="division_id" onchange="this.form.submit()"
                            class="text-xs font-semibold rounded-xl border-slate-300 text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Divisi</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}" {{ $selectedDivisionId == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedDate !== date('Y-m-d') || $selectedDivisionId)
                    <a href="{{ route('admin.dashboard') }}" class="p-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-medium transition" title="Reset ke Hari Ini">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Date Banner Indicator -->
            <div class="flex items-center justify-between p-4 bg-indigo-50 border border-indigo-100 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Menampilkan Data Tanggal:</span>
                        <h3 class="text-base font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                            @if ($selectedDate === date('Y-m-d'))
                                <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Hari Ini</span>
                            @endif
                        </h3>
                    </div>
                </div>

                @if ($attendances->count() > 0)
                    <div class="text-xs text-slate-500 font-medium">
                        <span class="font-bold text-amber-600">{{ $attendances->count() }} karyawan</span> tercatat cuti/sakit/libur (dikecualikan dari wajib lapor).
                    </div>
                @endif
            </div>

            <!-- 1. STAT CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Total Karyawan Aktif -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Karyawan Aktif</p>
                        <h4 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalActiveEmployees }}</h4>
                        <p class="text-xs text-slate-500 mt-1">Terdaftar dalam sistem</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Card 2: Wajib Lapor -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Wajib Lapor</p>
                        <h4 class="text-3xl font-extrabold text-indigo-600 mt-2">{{ $wajibLaporCount }}</h4>
                        <p class="text-xs text-slate-500 mt-1">Setelah dikurangi cuti/libur</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>

                <!-- Card 3: Laporan Masuk -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Laporan Masuk</p>
                        <h4 class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $submittedCount }}</h4>
                        <p class="text-xs text-slate-500 mt-1">{{ $unsubmittedEmployees->count() }} belum melapor</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Card 4: Persentase Kepatuhan -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kepatuhan</p>
                        <h4 class="text-3xl font-extrabold {{ $complianceRate >= 90 ? 'text-emerald-600' : ($complianceRate >= 70 ? 'text-amber-600' : 'text-rose-600') }} mt-2">
                            {{ $complianceRate }}%
                        </h4>
                        <div class="w-24 bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="h-1.5 rounded-full {{ $complianceRate >= 90 ? 'bg-emerald-500' : ($complianceRate >= 70 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $complianceRate }}%"></div>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl {{ $complianceRate >= 90 ? 'bg-emerald-50 text-emerald-600' : ($complianceRate >= 70 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }} flex items-center justify-center font-bold">
                        %
                    </div>
                </div>
            </div>

            <!-- 2. KARYAWAN BELUM MELAPOR -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span>Karyawan Belum Melapor</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $unsubmittedEmployees->count() > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $unsubmittedEmployees->count() }} Orang
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar karyawan yang belum mengirimkan laporan harian pada tanggal yang dipilih.</p>
                    </div>

                    @if ($unsubmittedEmployees->count() > 0)
                        <a href="{{ route('admin.attendances.create', ['date' => $selectedDate]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tandai Cuti / Sakit
                        </a>
                    @endif
                </div>

                @if ($unsubmittedEmployees->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-6">Nama Karyawan</th>
                                    <th class="py-3 px-6">Divisi</th>
                                    <th class="py-3 px-6">Status Kehadiran</th>
                                    <th class="py-3 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($unsubmittedEmployees as $emp)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3.5 px-6 font-semibold text-slate-900">
                                            {{ $emp->name }}
                                        </td>
                                        <td class="py-3.5 px-6">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                                {{ $emp->division->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-6">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-100">
                                                Belum Mengirim Laporan
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-6 text-right">
                                            <a href="{{ route('admin.attendances.create', ['date' => $selectedDate, 'employee_id' => $emp->id]) }}" 
                                               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                                Catat Cuti/Sakit &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Semua Karyawan Sudah Melapor!</h4>
                        <p class="text-xs text-slate-500 mt-1">Seluruh karyawan yang wajib lapor pada tanggal ini telah mengirimkan laporan harian.</p>
                    </div>
                @endif
            </div>

            <!-- 3. REKAP PER DIVISI -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-900">Rekap Kepatuhan Per Divisi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Perbandingan jumlah laporan yang masuk dibanding target karyawan per divisi.</p>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($divisionSummary as $div)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-sm text-slate-800">{{ $div['name'] }}</h4>
                                <span class="text-xs font-extrabold px-2 py-0.5 rounded-full {{ $div['percentage'] >= 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $div['percentage'] }}%
                                </span>
                            </div>

                            <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                                <span>Laporan: <strong class="text-slate-800">{{ $div['submitted'] }}</strong> / {{ $div['wajib_lapor'] }}</span>
                                <span>Total: {{ $div['total_employees'] }} staf</span>
                            </div>

                            <div class="w-full bg-slate-200 rounded-full h-2 mt-2 overflow-hidden">
                                <div class="h-2 rounded-full {{ $div['percentage'] >= 100 ? 'bg-emerald-500' : 'bg-indigo-600' }}" style="width: {{ $div['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 4. DAFTAR LAPORAN TERBARU -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Laporan Terbaru yang Masuk</h3>
                        <p class="text-xs text-slate-500 mt-0.5">10 pengiriman laporan harian paling mutakhir.</p>
                    </div>

                    <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                        Lihat Semua Laporan &rarr;
                    </a>
                </div>

                @if ($recentReports->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-6">Tanggal Laporan</th>
                                    <th class="py-3 px-6">Nama Karyawan</th>
                                    <th class="py-3 px-6">Divisi</th>
                                    <th class="py-3 px-6">Waktu Submit</th>
                                    <th class="py-3 px-6 text-right">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($recentReports as $report)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-6 font-semibold text-slate-900">
                                            {{ $report->report_date->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="py-3 px-6">
                                            <div class="font-medium text-slate-900">{{ $report->employee_name_snapshot }}</div>
                                            <div class="text-xs text-slate-400">{{ $report->email }}</div>
                                        </td>
                                        <td class="py-3 px-6">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                                {{ $report->division_name_snapshot }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-xs text-slate-500">
                                            {{ $report->submitted_at->format('H:i, d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-6 text-right">
                                            <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-xs text-slate-400">
                        Belum ada laporan harian yang masuk ke sistem.
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
