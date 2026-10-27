<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Buat Daily Report HRD') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Isi formulir monitoring karyawan atau kondisi kantor.</p>
            </div>

            <a href="{{ route('admin.hrd-reports.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Global Error Notification -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                    <div class="flex items-center gap-2 font-semibold text-sm">
                        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Terdapat beberapa kesalahan pengisian form:</span>
                    </div>
                    <ul class="mt-2 ml-7 list-disc text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden"
                 x-data="hrdReportForm()">

                <!-- Header Banner -->
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-8 text-white">
                    <div class="max-w-2xl">
                        <span class="inline-block px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold tracking-wide text-indigo-100 mb-3">
                            Formulir HRD
                        </span>
                        <h2 class="text-2xl font-bold tracking-tight">Daily Report HRD</h2>
                        <p class="mt-2 text-sm text-indigo-100">
                            Buat laporan monitoring karyawan atau kondisi kantor harian.
                        </p>
                    </div>
                </div>

                <form action="{{ route('admin.hrd-reports.store') }}" method="POST" class="p-6 sm:p-8 space-y-8" @submit="isSubmitting = true">
                    @csrf

                    <!-- BAGIAN 1: Tanggal & Jenis Monitoring -->
                    <div class="space-y-6">
                        <div class="border-b border-slate-200 pb-3">
                            <h3 class="text-base font-semibold text-slate-800">Informasi Laporan</h3>
                            <p class="text-xs text-slate-500">Pilih tanggal dan jenis monitoring yang akan dilaporkan.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Tanggal Laporan -->
                            <div>
                                <label for="report_date" class="block text-sm font-semibold text-slate-700 mb-1">
                                    Tanggal Laporan <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="report_date" id="report_date"
                                       value="{{ old('report_date', date('Y-m-d')) }}"
                                       class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('report_date')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jenis Monitoring -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">
                                    Jenis Monitoring <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex gap-4 mt-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="report_type" value="employee"
                                               x-model="reportType"
                                               class="text-indigo-600 focus:ring-indigo-500"
                                               {{ old('report_type', 'employee') === 'employee' ? 'checked' : '' }}>
                                        <span class="text-sm text-slate-700">Monitoring Karyawan</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="report_type" value="office"
                                               x-model="reportType"
                                               class="text-indigo-600 focus:ring-indigo-500"
                                               {{ old('report_type') === 'office' ? 'checked' : '' }}>
                                        <span class="text-sm text-slate-700">Kondisi Kantor</span>
                                    </label>
                                </div>
                                @error('report_type')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 2: MONITORING KARYAWAN -->
                    <div x-show="reportType === 'employee'" x-cloak class="space-y-6">
                        <div class="border-b border-slate-200 pb-3">
                            <h3 class="text-base font-semibold text-slate-800">Identitas Karyawan</h3>
                            <p class="text-xs text-slate-500">Pilih karyawan dari daftar atau input manual jika belum terdaftar.</p>
                        </div>

                        <!-- Mode Input -->
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="input_mode" value="select"
                                           x-model="inputMode"
                                           class="text-indigo-600 focus:ring-indigo-500"
                                           {{ old('input_mode', 'select') === 'select' ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-700">Pilih dari Daftar Karyawan</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="input_mode" value="manual"
                                           x-model="inputMode"
                                           class="text-indigo-600 focus:ring-indigo-500"
                                           {{ old('input_mode') === 'manual' ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-700">Input Manual</span>
                                </label>
                            </div>
                            @error('input_mode')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror

                            <!-- Dropdown Karyawan -->
                            <div x-show="inputMode === 'select'">
                                <label for="employee_id" class="block text-sm font-semibold text-slate-700 mb-1">
                                    Nama Karyawan <span class="text-rose-500">*</span>
                                </label>
                                <select name="employee_id" id="employee_id"
                                        class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Input Manual -->
                            <div x-show="inputMode === 'manual'">
                                <label for="manual_employee_name" class="block text-sm font-semibold text-slate-700 mb-1">
                                    Nama Karyawan (Manual) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="manual_employee_name" id="manual_employee_name"
                                       value="{{ old('manual_employee_name') }}"
                                       placeholder="Ketik nama karyawan..."
                                       class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('manual_employee_name')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Kategori Monitoring Karyawan -->
                        <div class="space-y-4">
                            <div class="border-b border-slate-200 pb-3">
                                <h3 class="text-base font-semibold text-slate-800">Kategori Monitoring</h3>
                                <p class="text-xs text-slate-500">Pilih satu atau beberapa kategori yang relevan. Setiap kategori memiliki catatan detail tersendiri.</p>
                            </div>
                            @error('categories')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror

                            <div class="space-y-4">
                                <template x-for="(cat, index) in employeeCategories" :key="cat.key">
                                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                                        <label class="flex items-center gap-3 p-4 cursor-pointer hover:bg-slate-50 transition"
                                               :class="cat.selected ? 'bg-indigo-50/50 border-indigo-200' : ''">
                                            <input type="checkbox" x-model="cat.selected"
                                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm font-medium text-slate-700" x-text="cat.label"></span>
                                        </label>

                                        <div x-show="cat.selected" x-cloak class="px-4 pb-4">
                                            <input type="hidden" :name="'categories[' + getSelectedIndex(cat.key) + '][category]'" :value="cat.key" x-bind:disabled="!cat.selected">
                                            <label class="block text-xs font-semibold text-slate-600 mb-1">Detail Catatan <span class="text-rose-500">*</span></label>
                                            <textarea :name="'categories[' + getSelectedIndex(cat.key) + '][notes]'"
                                                      rows="3"
                                                      x-model="cat.notes"
                                                      placeholder="Tulis catatan monitoring untuk kategori ini..."
                                                      class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                      x-bind:disabled="!cat.selected"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 3: MONITORING KONDISI KANTOR -->
                    <div x-show="reportType === 'office'" x-cloak class="space-y-6">
                        <div class="border-b border-slate-200 pb-3">
                            <h3 class="text-base font-semibold text-slate-800">Monitoring Kondisi Kantor</h3>
                            <p class="text-xs text-slate-500">Pilih kategori kondisi kantor yang ingin dilaporkan beserta detail temuan dan tindakan.</p>
                        </div>
                        @error('office_items')
                            <p class="text-xs text-rose-500">{{ $message }}</p>
                        @enderror

                        <div class="space-y-4">
                            <template x-for="(item, index) in officeCategories" :key="item.key">
                                <div class="border border-slate-200 rounded-xl overflow-hidden">
                                    <label class="flex items-center gap-3 p-4 cursor-pointer hover:bg-slate-50 transition"
                                           :class="item.selected ? 'bg-emerald-50/50 border-emerald-200' : ''">
                                        <input type="checkbox" x-model="item.selected"
                                               class="rounded text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-sm font-medium text-slate-700" x-text="item.label"></span>
                                    </label>

                                    <div x-show="item.selected" x-cloak class="px-4 pb-4 space-y-4">
                                        <input type="hidden" :name="'office_items[' + getOfficeSelectedIndex(item.key) + '][category]'" :value="item.key" x-bind:disabled="!item.selected">

                                        <!-- Detail Temuan -->
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 mb-1">Detail Temuan <span class="text-rose-500">*</span></label>
                                            <textarea :name="'office_items[' + getOfficeSelectedIndex(item.key) + '][finding]'"
                                                      rows="3"
                                                      x-model="item.finding"
                                                      placeholder="Jelaskan temuan kondisi kantor..."
                                                      class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                      x-bind:disabled="!item.selected"></textarea>
                                        </div>

                                        <!-- Pilihan Tindakan HRD -->
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 mb-1">Tindakan HRD <span class="text-rose-500">*</span></label>
                                            <select :name="'office_items[' + getOfficeSelectedIndex(item.key) + '][action]'"
                                                    x-model="item.action"
                                                    class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                    x-bind:disabled="!item.selected">
                                                <option value="">-- Pilih Tindakan --</option>
                                                <option value="tidak_perlu_tindakan">Tidak perlu tindakan</option>
                                                <option value="mengingatkan_karyawan">Mengingatkan karyawan</option>
                                                <option value="koordinasi_atasan">Koordinasi dengan atasan</option>
                                                <option value="melaporkan_kerusakan">Melaporkan kerusakan</option>
                                                <option value="mengajukan_perbaikan">Mengajukan perbaikan</option>
                                            </select>
                                        </div>

                                        <!-- Catatan Tindakan -->
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan Tindakan <span class="text-rose-500">*</span></label>
                                            <textarea :name="'office_items[' + getOfficeSelectedIndex(item.key) + '][action_notes]'"
                                                      rows="2"
                                                      x-model="item.actionNotes"
                                                      placeholder="Jelaskan detail tindakan yang dilakukan..."
                                                      class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                      x-bind:disabled="!item.selected"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex justify-end pt-4 border-t border-slate-200">
                        <button type="submit"
                                :disabled="isSubmitting"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Laporan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function hrdReportForm() {
            return {
                reportType: '{{ old('report_type', 'employee') }}',
                inputMode: '{{ old('input_mode', 'select') }}',
                isSubmitting: false,

                employeeCategories: [
                    { key: 'kedisiplinan_karyawan', label: 'Kedisiplinan Karyawan', selected: false, notes: '' },
                    { key: 'sikap_perilaku', label: 'Sikap dan Perilaku', selected: false, notes: '' },
                    { key: 'aktivitas_tanggung_jawab', label: 'Aktivitas dan Tanggung Jawab Pekerjaan', selected: false, notes: '' },
                    { key: 'komunikasi_koordinasi', label: 'Komunikasi dan Koordinasi', selected: false, notes: '' },
                    { key: 'kepatuhan_aturan', label: 'Kepatuhan terhadap Aturan Perusahaan', selected: false, notes: '' },
                    { key: 'kerja_sama', label: 'Kerja Sama Antarkaryawan', selected: false, notes: '' },
                    { key: 'kendala_permasalahan', label: 'Kendala / Permasalahan Karyawan', selected: false, notes: '' },
                    { key: 'hal_positif_apresiasi', label: 'Hal Positif / Apresiasi Karyawan', selected: false, notes: '' },
                    { key: 'temuan_lainnya', label: 'Temuan Lainnya', selected: false, notes: '' },
                ],

                officeCategories: [
                    { key: 'kebersihan_kerapian', label: 'Kebersihan dan Kerapian', selected: false, finding: '', action: '', actionNotes: '' },
                    { key: 'fasilitas_peralatan', label: 'Fasilitas dan Peralatan', selected: false, finding: '', action: '', actionNotes: '' },
                    { key: 'keamanan_kenyamanan_k3', label: 'Keamanan, Kenyamanan / Keselamatan Kerja', selected: false, finding: '', action: '', actionNotes: '' },
                ],

                getSelectedIndex(key) {
                    let selectedKeys = this.employeeCategories.filter(c => c.selected).map(c => c.key);
                    return selectedKeys.indexOf(key);
                },

                getOfficeSelectedIndex(key) {
                    let selectedKeys = this.officeCategories.filter(c => c.selected).map(c => c.key);
                    return selectedKeys.indexOf(key);
                },
            }
        }
    </script>
</x-app-layout>
