<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Catat Status Kehadiran / Cuti') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Tandai karyawan yang sedang cuti, sakit, izin, atau libur pada tanggal tertentu.</p>
            </div>

            <a href="{{ route('admin.attendances.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                <form method="POST" action="{{ route('admin.attendances.store') }}" class="space-y-6">
                    @csrf

                    <!-- Pilih Karyawan -->
                    <div>
                        <label for="employee_id" class="block text-sm font-semibold text-slate-700 mb-1">
                            Pilih Karyawan <span class="text-rose-500">*</span>
                        </label>
                        <select name="employee_id" id="employee_id" required autofocus
                                class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ (old('employee_id', $defaultEmployeeId) == $employee->id) ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->division->name ?? 'Tanpa Divisi' }})
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label for="date" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="date" id="date" value="{{ old('date', $defaultDate) }}" required
                               class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('date')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">
                            Status Kehadiran <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="cuti" {{ old('status') === 'cuti' ? 'selected' : '' }}>Cuti Tahunan / Melahirkan</option>
                            <option value="sakit" {{ old('status') === 'sakit' ? 'selected' : '' }}>Sakit (Surat Dokter)</option>
                            <option value="izin" {{ old('status') === 'izin' ? 'selected' : '' }}>Izin Khusus</option>
                            <option value="libur" {{ old('status') === 'libur' ? 'selected' : '' }}>Libur / Off Day</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan / Keterangan -->
                    <div>
                        <label for="note" class="block text-sm font-semibold text-slate-700 mb-1">
                            Keterangan Tambahan
                        </label>
                        <input type="text" name="note" id="note" value="{{ old('note') }}"
                               placeholder="Contoh: Cuti mudik lebaran, sakit demam..."
                               class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('note')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.attendances.index') }}" 
                           class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold shadow hover:bg-indigo-700 transition">
                            Simpan Kehadiran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
