<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ __('Buat Daily Report HRD') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Isi formulir monitoring karyawan, kondisi kantor, atau keduanya sekaligus.</p>
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
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
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
                            Centang jenis monitoring yang ingin dilaporkan. Anda dapat mengisi Monitoring Karyawan saja, Kondisi Kantor saja, atau mengisi keduanya sekaligus dalam satu laporan.
                        </p>
                    </div>
                </div>

                <form action="{{ route('admin.hrd-reports.store') }}" method="POST" class="p-6 sm:p-8 space-y-8" @submit="isSubmitting = true">
                    @csrf

                    <!-- Hidden Input Report Type (Otomatis Sync) -->
                    <input type="hidden" name="report_type" :value="reportType">

                    <!-- 1. TANGGAL LAPORAN (FIELD BERSAMA) -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <label for="report_date" class="block text-sm font-bold text-slate-800 mb-0.5">
                                    Tanggal Laporan <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-xs text-slate-500">Seluruh entri dalam laporan ini akan mengacu pada tanggal ini.</p>
                            </div>
                            <div class="w-full sm:w-64">
                                <input type="date" name="report_date" id="report_date"
                                       value="{{ old('report_date', date('Y-m-d')) }}"
                                       class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-medium">
                                @error('report_date')
                                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 2. SUSUNAN VERTIKAL JENIS MONITORING -->
                    <div class="space-y-6">

                        <!-- SECTION A: MONITORING KARYAWAN (BAGIAN ATAS) -->
                        <div class="border-2 rounded-2xl transition-all duration-200 overflow-hidden shadow-sm"
                             :class="isEmployeeActive ? 'border-indigo-500 bg-white ring-1 ring-indigo-500' : 'border-slate-200 bg-slate-50/60'">
                            
                            <!-- Header / Section Activation Bar -->
                            <div class="p-5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm transition"
                                         :class="isEmployeeActive ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-600'">
                                        1
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-base font-bold text-slate-900">Monitoring Karyawan</h3>
                                            <span x-show="isEmployeeActive" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700">Aktif</span>
                                            <span x-show="!isEmployeeActive" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-600">Tidak Disertakan</span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">Catat evaluasi, status tindak lanjut, dan detail catatan per kategori untuk satu atau beberapa karyawan.</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="isEmployeeActive" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        <span class="ml-2 text-xs font-semibold text-slate-700 hidden sm:inline" x-text="isEmployeeActive ? 'Sertakan' : 'Lewati'"></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Form Konten Monitoring Karyawan (Expandable) -->
                            <div x-show="isEmployeeActive" x-cloak class="p-5 sm:p-6 border-t border-indigo-100 bg-white space-y-6">
                                
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800">Daftar Entri Monitoring Karyawan</h4>
                                        <p class="text-xs text-slate-500">Anda dapat memasukkan beberapa karyawan dalam satu laporan.</p>
                                    </div>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700"
                                          x-text="'Total: ' + employees.length + ' Karyawan'"></span>
                                </div>

                                <!-- Loop Entri Karyawan -->
                                <div class="space-y-6">
                                    <template x-for="(emp, empIndex) in employees" :key="emp.id">
                                        <div class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-5 relative">
                                            
                                            <!-- Sub-Header Entri Karyawan -->
                                            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs font-bold"
                                                          x-text="empIndex + 1"></span>
                                                    <span class="text-sm font-bold text-slate-800"
                                                          x-text="'Karyawan #' + (empIndex + 1)"></span>
                                                </div>

                                                <!-- Tombol Hapus Entri (hanya tampil jika > 1) -->
                                                <button type="button"
                                                        x-show="employees.length > 1"
                                                        @click="removeEmployee(empIndex)"
                                                        class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 transition"
                                                        title="Hapus entri karyawan ini">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>

                                            <!-- Identitas Karyawan (Hybrid Mode) -->
                                            <div class="space-y-3">
                                                <div class="flex flex-wrap gap-4">
                                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                                        <input type="radio" :name="'employees[' + empIndex + '][input_mode]'" value="select"
                                                               x-model="emp.input_mode"
                                                               :disabled="!isEmployeeActive"
                                                               class="text-indigo-600 focus:ring-indigo-500 h-3.5 w-3.5">
                                                        <span>Pilih dari Master Karyawan</span>
                                                    </label>
                                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                                        <input type="radio" :name="'employees[' + empIndex + '][input_mode]'" value="manual"
                                                               x-model="emp.input_mode"
                                                               :disabled="!isEmployeeActive"
                                                               class="text-indigo-600 focus:ring-indigo-500 h-3.5 w-3.5">
                                                        <span>Input Manual (Karyawan Belum Terdaftar)</span>
                                                    </label>
                                                </div>

                                                <!-- Pilihan Dropdown Karyawan -->
                                                <div x-show="emp.input_mode === 'select'">
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                                        Pilih Karyawan <span class="text-rose-500">*</span>
                                                    </label>
                                                    <select :name="'employees[' + empIndex + '][employee_id]'"
                                                            x-model="emp.employee_id"
                                                            :disabled="!isEmployeeActive || emp.input_mode !== 'select'"
                                                            class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                        <option value="">-- Pilih Karyawan Aktif --</option>
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Input Nama Manual -->
                                                <div x-show="emp.input_mode === 'manual'" x-cloak>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                                        Nama Karyawan (Manual) <span class="text-rose-500">*</span>
                                                    </label>
                                                    <input type="text"
                                                           :name="'employees[' + empIndex + '][manual_employee_name]'"
                                                           x-model="emp.manual_employee_name"
                                                           :disabled="!isEmployeeActive || emp.input_mode !== 'manual'"
                                                           placeholder="Ketik nama lengkap karyawan..."
                                                           class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                                </div>
                                            </div>

                                            <!-- Kategori Monitoring Karyawan & Detail (Status & Catatan per Kategori) -->
                                            <div class="space-y-3 pt-2 border-t border-slate-200">
                                                <div class="flex items-center justify-between">
                                                    <label class="block text-xs font-bold text-slate-700">
                                                        Kategori Monitoring Karyawan <span class="text-rose-500">*</span>
                                                    </label>
                                                    <span class="text-[11px] text-slate-400">Pilih kategori untuk memunculkan Status dan Detail Catatan</span>
                                                </div>

                                                <div class="space-y-3">
                                                    <template x-for="(cat, cIdx) in emp.categories" :key="cat.key">
                                                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm transition"
                                                             :class="cat.selected ? 'border-indigo-300 ring-1 ring-indigo-200' : ''">
                                                            
                                                            <!-- Checkbox Pilihan Kategori -->
                                                            <label class="flex items-center gap-3 p-3.5 cursor-pointer hover:bg-slate-50 transition"
                                                                   :class="cat.selected ? 'bg-indigo-50/50 border-b border-indigo-100' : ''">
                                                                <input type="checkbox" x-model="cat.selected"
                                                                       class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                                                <span class="text-xs font-bold text-slate-800" x-text="cat.label"></span>
                                                            </label>

                                                            <!-- Bagian Detail Kategori (Status Tindak Lanjut HRD & Detail Catatan) -->
                                                            <!-- HANYA MUNCUL KETIKA KATEGORI DIPILIH -->
                                                            <div x-show="cat.selected" x-cloak class="p-4 bg-slate-50/50 space-y-3.5 border-t border-slate-100">
                                                                
                                                                <!-- Hidden input kategori terkirim hanya jika kategori dipilih -->
                                                                <input type="hidden"
                                                                       :name="'employees[' + empIndex + '][categories][' + getSelectedCategoryIndex(emp, cat.key) + '][category]'"
                                                                       :value="cat.key"
                                                                       :disabled="!isEmployeeActive || !cat.selected">
                                                                
                                                                <!-- A. Dropdown Status Tindak Lanjut HRD (Per Kategori) -->
                                                                <div>
                                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                                                        Status Tindak Lanjut HRD <span class="text-rose-500">*</span>
                                                                    </label>
                                                                    <select :name="'employees[' + empIndex + '][categories][' + getSelectedCategoryIndex(emp, cat.key) + '][follow_up_status]'"
                                                                            x-model="cat.follow_up_status"
                                                                            :disabled="!isEmployeeActive || !cat.selected"
                                                                            class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white font-medium">
                                                                        <option value="">-- Pilih Status Tindak Lanjut --</option>
                                                                        <option value="selesai">Selesai</option>
                                                                        <option value="dalam_proses">Dalam proses</option>
                                                                        <option value="menunggu_pihak_terkait">Menunggu pihak terkait</option>
                                                                        <option value="perlu_monitoring_lanjutan">Perlu monitoring lanjutan</option>
                                                                        <option value="memerlukan_keputusan_direktur">Memerlukan keputusan Direktur</option>
                                                                    </select>
                                                                </div>

                                                                <!-- B. Kolom Teks Detail Catatan (Per Kategori) -->
                                                                <div>
                                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                                                        Detail Catatan <span class="text-rose-500">*</span>
                                                                    </label>
                                                                    <textarea :name="'employees[' + empIndex + '][categories][' + getSelectedCategoryIndex(emp, cat.key) + '][notes]'"
                                                                              rows="3"
                                                                              x-model="cat.notes"
                                                                              :disabled="!isEmployeeActive || !cat.selected"
                                                                              placeholder="Ketik detail hasil monitoring untuk kategori ini..."
                                                                              class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white leading-relaxed"></textarea>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                        </div>
                                    </template>
                                </div>

                                <!-- Tombol Tambah Karyawan -->
                                <div class="pt-2">
                                    <button type="button"
                                            @click="addEmployee()"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-dashed border-indigo-300 text-indigo-700 bg-indigo-50/50 hover:bg-indigo-100 hover:border-indigo-400 text-xs font-bold transition w-full justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Tambah Karyawan
                                    </button>
                                </div>

                            </div>
                        </div>


                        <!-- SECTION B: MONITORING KONDISI KANTOR (BAGIAN BAWAH) -->
                        <div class="border-2 rounded-2xl transition-all duration-200 overflow-hidden shadow-sm"
                             :class="isOfficeActive ? 'border-emerald-500 bg-white ring-1 ring-emerald-500' : 'border-slate-200 bg-slate-50/60'">
                            
                            <!-- Header / Section Activation Bar -->
                            <div class="p-5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm transition"
                                         :class="isOfficeActive ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600'">
                                        2
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-base font-bold text-slate-900">Monitoring Kondisi Kantor</h3>
                                            <span x-show="isOfficeActive" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                                            <span x-show="!isOfficeActive" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-600">Tidak Disertakan</span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">Catat temuan kebersihan dan kerapian, fasilitas peralatan, serta keamanan/keselamatan kerja kantor.</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="isOfficeActive" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                        <span class="ml-2 text-xs font-semibold text-slate-700 hidden sm:inline" x-text="isOfficeActive ? 'Sertakan' : 'Lewati'"></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Form Konten Monitoring Kondisi Kantor (Expandable) -->
                            <div x-show="isOfficeActive" x-cloak class="p-5 sm:p-6 border-t border-emerald-100 bg-white space-y-6">
                                
                                <div class="border-b border-slate-100 pb-3">
                                    <h4 class="text-sm font-bold text-slate-800">Kategori Kondisi Kantor & Tindakan HRD</h4>
                                    <p class="text-xs text-slate-500">Pilih kategori yang ingin dilaporkan, tulis temuan, tentukan tindakan HRD, dan catatan tindakan.</p>
                                </div>

                                <div class="space-y-4">
                                    <template x-for="(item, index) in officeCategories" :key="item.key">
                                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                                            
                                            <!-- Checkbox Kategori Kantor -->
                                            <label class="flex items-center gap-3 p-4 cursor-pointer hover:bg-slate-50 transition"
                                                   :class="item.selected ? 'bg-emerald-50/50 border-b border-slate-200' : ''">
                                                <input type="checkbox" x-model="item.selected"
                                                       class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                                <span class="text-sm font-semibold text-slate-800" x-text="item.label"></span>
                                            </label>

                                            <!-- Field Detail Temuan & Tindakan -->
                                            <div x-show="item.selected" x-cloak class="p-4 sm:p-5 bg-slate-50/40 space-y-4">
                                                <input type="hidden"
                                                       :name="'office_items[' + getOfficeSelectedIndex(item.key) + '][category]'"
                                                       :value="item.key"
                                                       :disabled="!isOfficeActive || !item.selected">

                                                <!-- Detail Temuan -->
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                                        Detail Temuan <span class="text-rose-500">*</span>
                                                    </label>
                                                    <textarea :name="'office_items[' + getOfficeSelectedIndex(item.key) + '][finding]'"
                                                              rows="3"
                                                              x-model="item.finding"
                                                              :disabled="!isOfficeActive || !item.selected"
                                                              placeholder="Jelaskan temuan kondisi fasilitas, kebersihan, atau keamanan kantor..."
                                                              class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white leading-relaxed"></textarea>
                                                </div>

                                                <!-- Pilihan Tindakan HRD (5 Opsi Resmi) -->
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                                        Tindakan HRD <span class="text-rose-500">*</span>
                                                    </label>
                                                    <select :name="'office_items[' + getOfficeSelectedIndex(item.key) + '][action]'"
                                                            x-model="item.action"
                                                            :disabled="!isOfficeActive || !item.selected"
                                                            class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white font-medium">
                                                        <option value="">-- Pilih Tindakan HRD --</option>
                                                        <option value="tidak_perlu_tindakan">Tidak perlu tindakan</option>
                                                        <option value="mengingatkan_karyawan">Mengingatkan karyawan</option>
                                                        <option value="koordinasi_atasan">Koordinasi dengan atasan</option>
                                                        <option value="melaporkan_kerusakan">Melaporkan kerusakan</option>
                                                        <option value="mengajukan_perbaikan">Mengajukan perbaikan</option>
                                                    </select>
                                                </div>

                                                <!-- Catatan Tindakan HRD -->
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                                        Detail Catatan Tindakan <span class="text-rose-500">*</span>
                                                    </label>
                                                    <textarea :name="'office_items[' + getOfficeSelectedIndex(item.key) + '][action_notes]'"
                                                              rows="2"
                                                              x-model="item.actionNotes"
                                                              :disabled="!isOfficeActive || !item.selected"
                                                              placeholder="Jelaskan rincian tindakan atau tindak lanjut yang dilakukan HRD..."
                                                              class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white leading-relaxed"></textarea>
                                                </div>
                                            </div>

                                        </div>
                                    </template>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- 3. TOMBOL SUBMIT -->
                    <div class="flex justify-end pt-4 border-t border-slate-200">
                        <button type="submit"
                                :disabled="isSubmitting || (!isEmployeeActive && !isOfficeActive)"
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
            @php
                $initialReportType = old('report_type', 'employee');
                $initEmployeeActive = ($initialReportType === 'employee' || $initialReportType === 'both' || old('employees') !== null);
                $initOfficeActive = ($initialReportType === 'office' || $initialReportType === 'both' || old('office_items') !== null);
                if (!$initEmployeeActive && !$initOfficeActive) {
                    $initEmployeeActive = true;
                }
            @endphp

            return {
                isEmployeeActive: {{ $initEmployeeActive ? 'true' : 'false' }},
                isOfficeActive: {{ $initOfficeActive ? 'true' : 'false' }},
                isSubmitting: false,

                get reportType() {
                    if (this.isEmployeeActive && this.isOfficeActive) return 'both';
                    if (this.isEmployeeActive) return 'employee';
                    if (this.isOfficeActive) return 'office';
                    return '';
                },

                // Template master 9 kategori karyawan
                masterCategories: [
                    { key: 'kedisiplinan_karyawan', label: 'Kedisiplinan Karyawan' },
                    { key: 'sikap_perilaku', label: 'Sikap dan Perilaku' },
                    { key: 'aktivitas_tanggung_jawab', label: 'Aktivitas dan Tanggung Jawab Pekerjaan' },
                    { key: 'komunikasi_koordinasi', label: 'Komunikasi dan Koordinasi' },
                    { key: 'kepatuhan_aturan', label: 'Kepatuhan terhadap Aturan Perusahaan' },
                    { key: 'kerja_sama', label: 'Kerja Sama Antarkaryawan' },
                    { key: 'kendala_permasalahan', label: 'Kendala / Permasalahan Karyawan' },
                    { key: 'hal_positif_apresiasi', label: 'Hal Positif / Apresiasi Karyawan' },
                    { key: 'temuan_lainnya', label: 'Temuan Lainnya' },
                ],

                // Array karyawan dinamis
                employees: [],

                // Template 3 kategori kantor
                officeCategories: [
                    { key: 'kebersihan_kerapian', label: 'Kebersihan dan Kerapian', selected: false, finding: '', action: '', actionNotes: '' },
                    { key: 'fasilitas_peralatan', label: 'Fasilitas dan Peralatan', selected: false, finding: '', action: '', actionNotes: '' },
                    { key: 'keamanan_kenyamanan_k3', label: 'Keamanan, Kenyamanan / Keselamatan Kerja', selected: false, finding: '', action: '', actionNotes: '' },
                ],

                init() {
                    // Buat minimal 1 entri karyawan awal
                    this.addEmployee();
                },

                createEmployeeEntry() {
                    return {
                        id: Date.now() + Math.random(),
                        input_mode: 'select',
                        employee_id: '',
                        manual_employee_name: '',
                        categories: this.masterCategories.map(cat => ({
                            key: cat.key,
                            label: cat.label,
                            selected: false,
                            follow_up_status: '',
                            notes: ''
                        }))
                    };
                },

                addEmployee() {
                    this.employees.push(this.createEmployeeEntry());
                },

                removeEmployee(index) {
                    if (this.employees.length > 1) {
                        this.employees.splice(index, 1);
                    }
                },

                getSelectedCategoryIndex(emp, catKey) {
                    let selectedKeys = emp.categories.filter(c => c.selected).map(c => c.key);
                    return selectedKeys.indexOf(catKey);
                },

                getOfficeSelectedIndex(key) {
                    let selectedKeys = this.officeCategories.filter(c => c.selected).map(c => c.key);
                    return selectedKeys.indexOf(key);
                },
            }
        }
    </script>
</x-app-layout>
