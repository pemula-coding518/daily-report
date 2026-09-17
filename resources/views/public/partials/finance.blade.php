<div class="space-y-6">
    <div class="border-b border-slate-200 pb-3">
        <h3 class="text-base font-semibold text-slate-800">Form Laporan: Finance</h3>
        <p class="text-xs text-slate-500">Lengkapi data keuangan, pembuatan invoice, penerimaan/pengeluaran kas dan bank hari ini.</p>
    </div>

    <!-- Pekerjaan yang Dikerjakan Hari Ini -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Pekerjaan yang Dikerjakan Hari Ini <span class="text-rose-500">*</span>
        </label>
        <textarea name="form_data[pekerjaan_hari_ini]" rows="3" placeholder="Tuliskan aktivitas finance hari ini..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.pekerjaan_hari_ini') }}</textarea>
        @error('form_data.pekerjaan_hari_ini')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Invoice yang Dibuat & Pembayaran -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Invoice yang Dibuat
            </label>
            <input type="text" name="form_data[invoice_dibuat]" 
                   value="{{ old('form_data.invoice_dibuat') }}"
                   placeholder="Contoh: INV-2026-001 (PT ABC), INV-2026-002 (CV XYZ)"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.invoice_dibuat')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Pembayaran / Penerimaan
            </label>
            <input type="text" name="form_data[pembayaran]" 
                   value="{{ old('form_data.pembayaran') }}"
                   placeholder="Contoh: Pembayaran invoice vendor server, pelunasan PT ABC"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.pembayaran')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Rekap Kas atau Bank -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Rekap Kas atau Bank
        </label>
        <textarea name="form_data[rekap_kas_bank]" rows="2" placeholder="Tuliskan catatan mutasi / rekonsiliasi kas dan bank..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.rekap_kas_bank') }}</textarea>
        @error('form_data.rekap_kas_bank')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kendala -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Kendala
        </label>
        <textarea name="form_data[kendala]" rows="3" placeholder="Tulis kendala perpajakan atau keuangan jika ada..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.kendala') }}</textarea>
        @error('form_data.kendala')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Rencana Pekerjaan Besok -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Rencana Pekerjaan Besok
        </label>
        <textarea name="form_data[rencana_besok]" rows="3" placeholder="Tulis rencana aktivitas finance besok..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.rencana_besok') }}</textarea>
        @error('form_data.rencana_besok')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>
</div>
