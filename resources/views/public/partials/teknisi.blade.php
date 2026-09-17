<div class="space-y-6">
    <div class="border-b border-slate-200 pb-3">
        <h3 class="text-base font-semibold text-slate-800">Form Laporan: Teknisi</h3>
        <p class="text-xs text-slate-500">Lengkapi detail pekerjaan teknis Anda hari ini.</p>
    </div>

    <!-- Jenis Pekerjaan Hari Ini (Checkboxes) -->
    <div x-data="{ 
        selectedJobs: {{ json_encode((array) old('form_data.pekerjaan_hari_ini', [])) }},
        hasOther() { return this.selectedJobs.includes('lainnya'); }
    }">
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Jenis Pekerjaan Hari Ini <span class="text-rose-500">*</span> <span class="text-xs font-normal text-slate-400">(Dapat memilih lebih dari satu)</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @php
                $jobOptions = [
                    'instalasi' => 'Instalasi',
                    'maintenance' => 'Maintenance',
                    'troubleshooting' => 'Troubleshooting',
                    'survey' => 'Survey',
                    'remote_support' => 'Remote Support',
                    'lainnya' => 'Yang lain',
                ];
            @endphp

            @foreach ($jobOptions as $val => $label)
                <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="form_data[pekerjaan_hari_ini][]" value="{{ $val }}"
                           x-model="selectedJobs"
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-slate-700 font-medium">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('form_data.pekerjaan_hari_ini')
            <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
        @enderror

        <!-- Input Jika Memilih 'Yang lain' -->
        <div x-show="hasOther()" x-cloak class="mt-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">
                Jelaskan Pekerjaan Lainnya <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="form_data[pekerjaan_lainnya]" 
                   value="{{ old('form_data.pekerjaan_lainnya') }}"
                   placeholder="Contoh: Kalibrasi perangkat sensor di ruang server"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.pekerjaan_lainnya')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Status Pekerjaan -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Status Pekerjaan Hari Ini <span class="text-rose-500">*</span>
        </label>
        <div class="grid grid-cols-3 gap-3">
            @foreach(['selesai' => 'Selesai', 'progres' => 'Progres', 'pending' => 'Pending'] as $val => $label)
                <label class="flex items-center justify-center p-3 rounded-xl border border-slate-200 hover:border-indigo-400 cursor-pointer transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50">
                    <input type="radio" name="form_data[status_pekerjaan]" value="{{ $val }}" 
                           {{ old('form_data.status_pekerjaan') == $val ? 'checked' : '' }}
                           class="text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm font-medium text-slate-700">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('form_data.status_pekerjaan')
            <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kendala -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Kendala / Hambatan
        </label>
        <textarea name="form_data[kendala]" rows="3" placeholder="Tulis kendala yang dihadapi jika ada..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.kendala') }}</textarea>
        @error('form_data.kendala')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Rencana Besok -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Rencana Besok
        </label>
        <textarea name="form_data[rencana_besok]" rows="3" placeholder="Tulis rencana pekerjaan yang akan dilakukan besok..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.rencana_besok') }}</textarea>
        @error('form_data.rencana_besok')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>
</div>
