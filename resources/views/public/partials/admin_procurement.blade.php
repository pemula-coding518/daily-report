<div class="space-y-6">
    <div class="border-b border-slate-200 pb-3">
        <h3 class="text-base font-semibold text-slate-800">Form Laporan: Admin Procurement</h3>
        <p class="text-xs text-slate-500">Lengkapi data pengadaan, PO, dan pengiriman barang hari ini.</p>
    </div>

    <!-- Pekerjaan yang Dikerjakan Hari Ini -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Pekerjaan yang Dikerjakan Hari Ini <span class="text-rose-500">*</span>
        </label>
        <textarea name="form_data[pekerjaan_hari_ini]" rows="3" placeholder="Tuliskan pekerjaan pengadaan hari ini..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.pekerjaan_hari_ini') }}</textarea>
        @error('form_data.pekerjaan_hari_ini')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Jumlah PO & Vendor yang Dihubungi -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Jumlah PO Dibuat <span class="text-rose-500">*</span>
            </label>
            <input type="number" min="0" name="form_data[jumlah_po]" 
                   value="{{ old('form_data.jumlah_po', 0) }}"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.jumlah_po')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Vendor yang Dihubungi
            </label>
            <input type="text" name="form_data[vendor_dihubungi]" 
                   value="{{ old('form_data.vendor_dihubungi') }}"
                   placeholder="Contoh: PT Sumber Makmur, CV Abadi Jaya"
                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('form_data.vendor_dihubungi')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Barang yang Diterima atau Dikirim -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Barang yang Diterima atau Dikirim
        </label>
        <textarea name="form_data[barang_diterima_dikirim]" rows="2" placeholder="Tulis rincian barang masuk/keluar hari ini..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.barang_diterima_dikirim') }}</textarea>
        @error('form_data.barang_diterima_dikirim')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kendala -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">
            Kendala
        </label>
        <textarea name="form_data[kendala]" rows="3" placeholder="Tulis kendala pengadaan jika ada..."
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
        <textarea name="form_data[rencana_besok]" rows="3" placeholder="Tulis rencana pengadaan besok..."
                  class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('form_data.rencana_besok') }}</textarea>
        @error('form_data.rencana_besok')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>
</div>
