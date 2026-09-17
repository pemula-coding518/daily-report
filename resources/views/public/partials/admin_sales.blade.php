<div class="space-y-6">
    <div class="border-b border-slate-200 pb-3">
        <h3 class="text-base font-semibold text-slate-800">Form Laporan: Admin Sales</h3>
        <p class="text-xs text-slate-500">Lengkapi aktivitas penjualan, follow-up, dan closing hari ini.</p>
    </div>

    <!-- Pekerjaan Hari Ini -->
    <div x-data="{ 
        selectedJobs: {{ json_encode((array) old('form_data.pekerjaan_hari_ini', [])) }},
        hasOther() { return this.selectedJobs.includes('lainnya'); }
    }">
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Pekerjaan yang Dikerjakan Hari Ini <span class="text-rose-500">*</span> <span class="text-xs font-normal text-slate-400">(Bisa lebih dari satu)</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach(['follow_up' => 'Follow Up', 'membuat_penawaran' => 'Membuat Penawaran', 'lainnya' => 'Yang lain'] as $val => $label)
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
                Jelaskan Pekerjaan Lainnya Hari Ini <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="form_data[pekerjaan_hari_ini_lainnya]" 
                   value="{{ old('form_data.pekerjaan_hari_ini_lainnya') }}"
                   placeholder="Tuliskan pekerjaan lainnya..."
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.pekerjaan_hari_ini_lainnya')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Rencana Pekerjaan Besok -->
    <div x-data="{ 
        selectedTomorrow: {{ json_encode((array) old('form_data.rencana_besok', [])) }},
        hasOtherTomorrow() { return this.selectedTomorrow.includes('lainnya'); }
    }">
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Rencana Pekerjaan Besok <span class="text-rose-500">*</span> <span class="text-xs font-normal text-slate-400">(Bisa lebih dari satu)</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach(['follow_up' => 'Follow Up', 'membuat_penawaran' => 'Membuat Penawaran', 'lainnya' => 'Yang lain'] as $val => $label)
                <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="form_data[rencana_besok][]" value="{{ $val }}"
                           x-model="selectedTomorrow"
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-slate-700 font-medium">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('form_data.rencana_besok')
            <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
        @enderror

        <!-- Input Jika Memilih 'Yang lain' -->
        <div x-show="hasOtherTomorrow()" x-cloak class="mt-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">
                Jelaskan Rencana Lainnya Besok <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="form_data[rencana_besok_lainnya]" 
                   value="{{ old('form_data.rencana_besok_lainnya') }}"
                   placeholder="Tuliskan rencana pekerjaan lainnya..."
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.rencana_besok_lainnya')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Metrik Angka (Follow Up, Quotation, Closing) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Follow Up (Jumlah Customer) <span class="text-rose-500">*</span>
            </label>
            <input type="number" min="0" name="form_data[jumlah_customer]" 
                   value="{{ old('form_data.jumlah_customer', 0) }}"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.jumlah_customer')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Quotation Dibuat <span class="text-rose-500">*</span>
            </label>
            <input type="number" min="0" name="form_data[jumlah_quotation]" 
                   value="{{ old('form_data.jumlah_quotation', 0) }}"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.jumlah_quotation')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Closing Hari Ini <span class="text-rose-500">*</span>
            </label>
            <input type="number" min="0" name="form_data[jumlah_closing]" 
                   value="{{ old('form_data.jumlah_closing', 0) }}"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.jumlah_closing')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Kendala -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Kendala
        </label>
        <textarea name="form_data[kendala]" rows="3" placeholder="Tulis kendala yang dihadapi hari ini..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.kendala') }}</textarea>
        @error('form_data.kendala')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>
</div>
