<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Koreksi / Edit Laporan Harian') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Edit data laporan karyawan {{ $report->employee_name_snapshot }}.</p>
            </div>

            <a href="{{ route('admin.reports.show', $report) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800">
                &larr; Batal & Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                        <ul class="list-disc ml-4 space-y-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="division_id" value="{{ $report->division_id }}">
                    <input type="hidden" name="employee_id" value="{{ $report->employee_id }}">

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block font-semibold">Nama Karyawan</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $report->employee_name_snapshot }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">Divisi / Posisi</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $report->division_name_snapshot }}</span>
                        </div>
                    </div>

                    <!-- Tanggal Pekerjaan & Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="report_date" class="block text-xs font-semibold text-slate-700 mb-1">
                                Tanggal Pekerjaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="report_date" id="report_date" 
                                   value="{{ old('report_date', $report->report_date->format('Y-m-d')) }}" required
                                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('report_date')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                                Email Pengirim <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" 
                                   value="{{ old('email', $report->email) }}" required
                                   class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status Laporan -->
                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                            Status Laporan <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" {{ old('status', $report->status) === 'active' ? 'selected' : '' }}>Aktif (Dihitung dalam Kepatuhan)</option>
                            <option value="cancelled" {{ old('status', $report->status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <h4 class="text-sm font-bold text-slate-800 mb-4">Edit Isi Form Laporan ({{ $report->division_name_snapshot }})</h4>
                        
                        @php
                            $data = (array) $report->form_data;
                            $code = $report->division_code_snapshot;
                        @endphp

                        @if ($code === 'teknisi')
                            <div class="space-y-4 text-sm" x-data="{
                                selectedJobs: {{ json_encode((array) old('form_data.pekerjaan_hari_ini', $data['pekerjaan_hari_ini'] ?? [])) }},
                                hasOther() { return this.selectedJobs.includes('lainnya'); }
                            }">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-2">Jenis Pekerjaan:</label>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        @foreach(['instalasi' => 'Instalasi', 'maintenance' => 'Maintenance', 'troubleshooting' => 'Troubleshooting', 'survey' => 'Survey', 'remote_support' => 'Remote Support', 'lainnya' => 'Yang lain'] as $val => $label)
                                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 text-xs cursor-pointer">
                                                <input type="checkbox" name="form_data[pekerjaan_hari_ini][]" value="{{ $val }}"
                                                       x-model="selectedJobs"
                                                       class="rounded border-slate-300 text-indigo-600">
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div x-show="hasOther()" class="mt-2">
                                        <input type="text" name="form_data[pekerjaan_lainnya]" 
                                               value="{{ old('form_data.pekerjaan_lainnya', $data['pekerjaan_lainnya'] ?? '') }}"
                                               placeholder="Penjelasan pekerjaan lainnya..."
                                               class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pekerjaan:</label>
                                    <select name="form_data[status_pekerjaan]" class="w-full text-xs rounded-xl border-slate-300">
                                        @foreach(['selesai' => 'Selesai', 'progres' => 'Progres', 'pending' => 'Pending'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('form_data.status_pekerjaan', $data['status_pekerjaan'] ?? '') === $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kendala:</label>
                                    <textarea name="form_data[kendala]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.kendala', $data['kendala'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rencana Besok:</label>
                                    <textarea name="form_data[rencana_besok]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.rencana_besok', $data['rencana_besok'] ?? '') }}</textarea>
                                </div>
                            </div>

                        @elseif ($code === 'admin_sales')
                            <div class="space-y-4 text-sm" x-data="{
                                selectedJobs: {{ json_encode((array) old('form_data.pekerjaan_hari_ini', $data['pekerjaan_hari_ini'] ?? [])) }},
                                selectedTomorrow: {{ json_encode((array) old('form_data.rencana_besok', $data['rencana_besok'] ?? [])) }},
                                hasOther() { return this.selectedJobs.includes('lainnya'); },
                                hasOtherTomorrow() { return this.selectedTomorrow.includes('lainnya'); }
                            }">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-2">Pekerjaan Hari Ini:</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach(['follow_up' => 'Follow Up', 'membuat_penawaran' => 'Membuat Penawaran', 'lainnya' => 'Yang lain'] as $val => $label)
                                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 text-xs cursor-pointer">
                                                <input type="checkbox" name="form_data[pekerjaan_hari_ini][]" value="{{ $val }}" x-model="selectedJobs" class="rounded border-slate-300 text-indigo-600">
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div x-show="hasOther()" class="mt-2">
                                        <input type="text" name="form_data[pekerjaan_hari_ini_lainnya]" value="{{ old('form_data.pekerjaan_hari_ini_lainnya', $data['pekerjaan_hari_ini_lainnya'] ?? '') }}" class="w-full text-xs rounded-xl border-slate-300" placeholder="Pekerjaan lainnya...">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-2">Rencana Besok:</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach(['follow_up' => 'Follow Up', 'membuat_penawaran' => 'Membuat Penawaran', 'lainnya' => 'Yang lain'] as $val => $label)
                                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 text-xs cursor-pointer">
                                                <input type="checkbox" name="form_data[rencana_besok][]" value="{{ $val }}" x-model="selectedTomorrow" class="rounded border-slate-300 text-indigo-600">
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div x-show="hasOtherTomorrow()" class="mt-2">
                                        <input type="text" name="form_data[rencana_besok_lainnya]" value="{{ old('form_data.rencana_besok_lainnya', $data['rencana_besok_lainnya'] ?? '') }}" class="w-full text-xs rounded-xl border-slate-300" placeholder="Rencana lainnya...">
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Follow Up</label>
                                        <input type="number" min="0" name="form_data[jumlah_customer]" value="{{ old('form_data.jumlah_customer', $data['jumlah_customer'] ?? 0) }}" class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Quotation</label>
                                        <input type="number" min="0" name="form_data[jumlah_quotation]" value="{{ old('form_data.jumlah_quotation', $data['jumlah_quotation'] ?? 0) }}" class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Closing</label>
                                        <input type="number" min="0" name="form_data[jumlah_closing]" value="{{ old('form_data.jumlah_closing', $data['jumlah_closing'] ?? 0) }}" class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kendala:</label>
                                    <textarea name="form_data[kendala]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.kendala', $data['kendala'] ?? '') }}</textarea>
                                </div>
                            </div>

                        @elseif ($code === 'admin_project')
                            <div class="space-y-4 text-sm" x-data="{
                                selectedDocs: {{ json_encode((array) old('form_data.dokumen_diproses', $data['dokumen_diproses'] ?? [])) }},
                                toggleDoc(val) {
                                    if (val === 'tidak_ada') {
                                        if (this.selectedDocs.includes('tidak_ada')) this.selectedDocs = ['tidak_ada'];
                                    } else {
                                        this.selectedDocs = this.selectedDocs.filter(item => item !== 'tidak_ada');
                                    }
                                },
                                hasOther() { return this.selectedDocs.includes('lainnya'); }
                            }">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pekerjaan Hari Ini:</label>
                                    <textarea name="form_data[pekerjaan_hari_ini]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.pekerjaan_hari_ini', $data['pekerjaan_hari_ini'] ?? '') }}</textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Project:</label>
                                        <input type="text" name="form_data[nama_project]" value="{{ old('form_data.nama_project', $data['nama_project'] ?? '') }}" class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Progres Project (%):</label>
                                        <input type="number" min="0" max="100" name="form_data[progres_persen]" value="{{ old('form_data.progres_persen', $data['progres_persen'] ?? 0) }}" class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-2">Dokumen yang Diproses:</label>
                                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                                        @foreach(['po' => 'PO', 'bast' => 'BAST', 'invoice' => 'Invoice', 'tidak_ada' => 'Tidak ada', 'lainnya' => 'Yang lain'] as $val => $label)
                                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 text-xs cursor-pointer">
                                                <input type="checkbox" name="form_data[dokumen_diproses][]" value="{{ $val }}"
                                                       x-model="selectedDocs" @change="toggleDoc('{{ $val }}')"
                                                       class="rounded border-slate-300 text-indigo-600">
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div x-show="hasOther()" class="mt-2">
                                        <input type="text" name="form_data[dokumen_lainnya]" value="{{ old('form_data.dokumen_lainnya', $data['dokumen_lainnya'] ?? '') }}" class="w-full text-xs rounded-xl border-slate-300" placeholder="Dokumen lainnya...">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kendala:</label>
                                    <textarea name="form_data[kendala]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.kendala', $data['kendala'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rencana Besok:</label>
                                    <textarea name="form_data[rencana_besok]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.rencana_besok', $data['rencana_besok'] ?? '') }}</textarea>
                                </div>
                            </div>

                        @elseif ($code === 'admin_procurement')
                            <div class="space-y-4 text-sm">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pekerjaan Hari Ini:</label>
                                    <textarea name="form_data[pekerjaan_hari_ini]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.pekerjaan_hari_ini', $data['pekerjaan_hari_ini'] ?? '') }}</textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah PO:</label>
                                        <input type="number" min="0" name="form_data[jumlah_po]" value="{{ old('form_data.jumlah_po', $data['jumlah_po'] ?? 0) }}" class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Vendor Dihubungi:</label>
                                        <input type="text" name="form_data[vendor_dihubungi]" value="{{ old('form_data.vendor_dihubungi', $data['vendor_dihubungi'] ?? '') }}" class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Barang Diterima/Dikirim:</label>
                                    <textarea name="form_data[barang_diterima_dikirim]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.barang_diterima_dikirim', $data['barang_diterima_dikirim'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kendala:</label>
                                    <textarea name="form_data[kendala]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.kendala', $data['kendala'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rencana Besok:</label>
                                    <textarea name="form_data[rencana_besok]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.rencana_besok', $data['rencana_besok'] ?? '') }}</textarea>
                                </div>
                            </div>

                        @elseif ($code === 'system_informasi')
                            <div class="space-y-4 text-sm">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pekerjaan Hari Ini:</label>
                                    <textarea name="form_data[pekerjaan_hari_ini]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.pekerjaan_hari_ini', $data['pekerjaan_hari_ini'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pengerjaan:</label>
                                    <input type="text" name="form_data[status_pengerjaan]" value="{{ old('form_data.status_pengerjaan', $data['status_pengerjaan'] ?? '') }}" class="w-full text-xs rounded-xl border-slate-300">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kendala:</label>
                                    <textarea name="form_data[kendala]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.kendala', $data['kendala'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rencana Besok:</label>
                                    <textarea name="form_data[rencana_besok]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.rencana_besok', $data['rencana_besok'] ?? '') }}</textarea>
                                </div>
                            </div>

                        @elseif ($code === 'finance')
                            <div class="space-y-4 text-sm">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pekerjaan Hari Ini:</label>
                                    <textarea name="form_data[pekerjaan_hari_ini]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.pekerjaan_hari_ini', $data['pekerjaan_hari_ini'] ?? '') }}</textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Invoice Dibuat:</label>
                                        <input type="text" name="form_data[invoice_dibuat]" value="{{ old('form_data.invoice_dibuat', $data['invoice_dibuat'] ?? '') }}" class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pembayaran:</label>
                                        <input type="text" name="form_data[pembayaran]" value="{{ old('form_data.pembayaran', $data['pembayaran'] ?? '') }}" class="w-full text-xs rounded-xl border-slate-300">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rekap Kas/Bank:</label>
                                    <textarea name="form_data[rekap_kas_bank]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.rekap_kas_bank', $data['rekap_kas_bank'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kendala:</label>
                                    <textarea name="form_data[kendala]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.kendala', $data['kendala'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rencana Besok:</label>
                                    <textarea name="form_data[rencana_besok]" rows="2" class="w-full text-xs rounded-xl border-slate-300">{{ old('form_data.rencana_besok', $data['rencana_besok'] ?? '') }}</textarea>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Submit Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.reports.show', $report) }}" 
                           class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold shadow hover:bg-indigo-700 transition">
                            Simpan Perubahan Laporan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
