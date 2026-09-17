<div class="space-y-6">
    <div class="border-b border-slate-200 pb-3">
        <h3 class="text-base font-semibold text-slate-800">Form Laporan: Admin Project</h3>
        <p class="text-xs text-slate-500">Lengkapi progres pelaksanaan project dan pemrosesan dokumen.</p>
    </div>

    <!-- Pekerjaan yang Dikerjakan Hari Ini -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Pekerjaan yang Dikerjakan Hari Ini <span class="text-rose-500">*</span>
        </label>
        <textarea name="form_data[pekerjaan_hari_ini]" rows="3" placeholder="Tuliskan pekerjaan project yang Anda kerjakan..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.pekerjaan_hari_ini') }}</textarea>
        @error('form_data.pekerjaan_hari_ini')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nama Project & Progres Project -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Nama Project yang Dikerjakan <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="form_data[nama_project]" 
                   value="{{ old('form_data.nama_project') }}"
                   placeholder="Contoh: Implementasi Jaringan Kantor Cabang Surabaya"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.nama_project')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Progres Project (%) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input type="number" min="0" max="100" name="form_data[progres_persen]" 
                       value="{{ old('form_data.progres_persen', 0) }}"
                       class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 pr-8">
                <span class="absolute right-3 top-2.5 text-sm text-slate-400 font-semibold">%</span>
            </div>
            @error('form_data.progres_persen')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Dokumen yang Diproses (Mutually Exclusive for 'tidak_ada') -->
    <div x-data="{
        selectedDocs: {{ json_encode((array) old('form_data.dokumen_diproses', [])) }},
        toggleDoc(val) {
            if (val === 'tidak_ada') {
                if (this.selectedDocs.includes('tidak_ada')) {
                    this.selectedDocs = ['tidak_ada'];
                }
            } else {
                this.selectedDocs = this.selectedDocs.filter(item => item !== 'tidak_ada');
            }
        },
        hasOther() { return this.selectedDocs.includes('lainnya'); }
    }">
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            Dokumen yang Diproses <span class="text-rose-500">*</span> <span class="text-xs font-normal text-slate-400">(Dapat memilih lebih dari satu)</span>
        </label>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            @php
                $docOptions = [
                    'po' => 'PO',
                    'bast' => 'BAST',
                    'invoice' => 'Invoice',
                    'tidak_ada' => 'Tidak ada',
                    'lainnya' => 'Yang lain',
                ];
            @endphp

            @foreach ($docOptions as $val => $label)
                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="form_data[dokumen_diproses][]" value="{{ $val }}"
                           x-model="selectedDocs"
                           @change="toggleDoc('{{ $val }}')"
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-slate-700 font-medium">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('form_data.dokumen_diproses')
            <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
        @enderror

        <!-- Input Jika Memilih 'Yang lain' -->
        <div x-show="hasOther()" x-cloak class="mt-3">
            <label class="block text-xs font-semibold text-slate-700 mb-1">
                Jelaskan Dokumen Lainnya <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="form_data[dokumen_lainnya]" 
                   value="{{ old('form_data.dokumen_lainnya') }}"
                   placeholder="Contoh: Surat Jalan & Berita Acara Rekonsiliasi"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.dokumen_lainnya')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Kendala -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Kendala
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
        <textarea name="form_data[rencana_besok]" rows="3" placeholder="Tulis rencana pekerjaan project besok..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.rencana_besok') }}</textarea>
        @error('form_data.rencana_besok')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>
</div>
