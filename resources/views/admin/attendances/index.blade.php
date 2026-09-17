<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Pencatatan Kehadiran & Cuti Karyawan') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Karyawan dengan status cuti/sakit/izin/libur akan otomatis dikecualikan dari kewajiban lapor di dashboard.</p>
            </div>

            <a href="{{ route('admin.attendances.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold shadow hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Catat Kehadiran / Cuti
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

            <!-- Filter Bar -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <form method="GET" action="{{ route('admin.attendances.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Tanggal</label>
                        <input type="date" name="date" value="{{ request('date') }}"
                               class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status Kehadiran</label>
                        <select name="status" class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="cuti" {{ request('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="sakit" {{ request('status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="izin" {{ request('status') === 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="libur" {{ request('status') === 'libur' ? 'selected' : '' }}>Libur</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-900 transition">
                            Terapkan Filter
                        </button>
                        @if (request()->hasAny(['date', 'status']))
                            <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table of Attendances -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-6">Tanggal</th>
                                <th class="py-3.5 px-6">Nama Karyawan</th>
                                <th class="py-3.5 px-6">Divisi</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6">Catatan</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($attendances as $att)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-6 font-semibold text-slate-900">
                                        {{ $att->date->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="py-3.5 px-6 font-medium text-slate-800">
                                        {{ $att->employee->name ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-xs text-slate-500">
                                        {{ $att->employee->division->name ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-6">
                                        @php
                                            $badges = [
                                                'cuti' => 'bg-amber-100 text-amber-800',
                                                'sakit' => 'bg-rose-100 text-rose-800',
                                                'izin' => 'bg-blue-100 text-blue-800',
                                                'libur' => 'bg-purple-100 text-purple-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $badges[$att->status] ?? 'bg-slate-100 text-slate-800' }}">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 text-xs text-slate-500">
                                        {{ $att->note ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right">
                                        <form method="POST" action="{{ route('admin.attendances.destroy', $att) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kehadiran ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-xs text-slate-400">
                                        Belum ada data pencatatan cuti atau izin kehadiran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($attendances->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
