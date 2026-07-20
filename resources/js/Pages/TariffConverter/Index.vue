<template>
  <AppLayout>
    <div class="space-y-8 max-w-6xl mx-auto">

      <!-- Hero Header Section -->
      <div class="text-center space-y-3">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white">
          Konversi Matriks Tarif <span class="bg-gradient-to-r from-cyan-400 via-sky-400 to-indigo-400 bg-clip-text text-transparent">Horizontal ke Per Baris</span>
        </h2>
        <p class="text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
          Ubah master data tarif Excel menyamping menjadi format hirarki baris (Induk & Rincian Komponen) secara otomatis dan siap di-import ke SIMRS.
        </p>
      </div>

      <!-- Stepper Wizard -->
      <div class="glass-panel p-6 sm:p-8 relative overflow-hidden border border-slate-800">
        <div class="flex items-center justify-between relative z-10">
          <!-- Stepper Connector Line -->
          <div class="absolute left-6 right-6 top-5 -z-10 h-0.5 bg-slate-800">
            <div 
              class="h-full bg-gradient-to-r from-cyan-500 via-indigo-500 to-emerald-500 transition-all duration-500 ease-out"
              :style="{ width: ((currentStep - 1) / 3 * 100) + '%' }"
            ></div>
          </div>

          <!-- Step 1 Button -->
          <button 
            @click="currentStep = 1"
            class="flex flex-col items-center group focus:outline-none cursor-pointer"
          >
            <div :class="['step-badge', currentStep === 1 ? 'step-badge-active' : (hasData ? 'step-badge-completed' : 'step-badge-inactive')]">
              <span v-if="hasData && currentStep !== 1">✓</span>
              <span v-else>1</span>
            </div>
            <span :class="['text-xs font-bold mt-2.5 transition-colors', currentStep === 1 ? 'text-cyan-400' : 'text-slate-400']">
              1. Input Data
            </span>
          </button>

          <!-- Step 2 Button -->
          <button 
            @click="hasData && (currentStep = 2)"
            :disabled="!hasData"
            class="flex flex-col items-center group focus:outline-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <div :class="['step-badge', currentStep === 2 ? 'step-badge-active' : (isMapped ? 'step-badge-completed' : 'step-badge-inactive')]">
              <span v-if="isConverted && currentStep !== 2">✓</span>
              <span v-else>2</span>
            </div>
            <span :class="['text-xs font-bold mt-2.5 transition-colors', currentStep === 2 ? 'text-cyan-400' : 'text-slate-400']">
              2. Column Mapper
            </span>
          </button>

          <!-- Step 3 Button -->
          <button 
            @click="isConverted && (currentStep = 3)"
            :disabled="!isConverted"
            class="flex flex-col items-center group focus:outline-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <div :class="['step-badge', currentStep === 3 ? 'step-badge-active' : (isConverted ? 'step-badge-completed' : 'step-badge-inactive')]">
              <span v-if="isConverted && currentStep > 3">✓</span>
              <span v-else>3</span>
            </div>
            <span :class="['text-xs font-bold mt-2.5 transition-colors', currentStep === 3 ? 'text-cyan-400' : 'text-slate-400']">
              3. Data Preview & Validasi
            </span>
          </button>

          <!-- Step 4 Button -->
          <button 
            @click="isConverted && (currentStep = 4)"
            :disabled="!isConverted"
            class="flex flex-col items-center group focus:outline-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
          >
            <div :class="['step-badge', currentStep === 4 ? 'step-badge-active' : 'step-badge-inactive']">
              4
            </div>
            <span :class="['text-xs font-bold mt-2.5 transition-colors', currentStep === 4 ? 'text-cyan-400' : 'text-slate-400']">
              4. Export & Download
            </span>
          </button>
        </div>
      </div>

      <!-- Step 1: Input Data -->
      <div v-if="currentStep === 1" class="glass-panel p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
          <div>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 shadow-[0_0_10px_#22d3ee]"></span>
              Tahap 1: Upload File Master Tarif (.xlsx / .xls / .csv)
            </h3>
            <p class="text-xs text-slate-400 mt-1">Pilih berkas Excel (.xlsx, .xls) atau CSV dari komputer Anda, atau gunakan sampel simulasi.</p>
          </div>

          <button 
            @click="loadSampleData"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-cyan-300 font-semibold text-xs rounded-xl transition shadow-md flex items-center gap-2 cursor-pointer"
          >
            <span>✨ Muat Sampel Simulasi RS</span>
          </button>
        </div>

        <!-- Dropzone Area -->
        <div class="relative group">
          <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-cyan-500/30 to-indigo-500/30 opacity-50 blur group-hover:opacity-100 transition duration-300"></div>
          <div class="relative p-10 rounded-2xl bg-slate-900/90 border-2 border-dashed border-slate-700 group-hover:border-cyan-500/80 transition-all flex flex-col items-center justify-center text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center text-2xl shadow-inner">
              📊
            </div>
            <div>
              <p class="text-sm font-bold text-white">Unggah Berkas Excel / CSV Master Tarif</p>
              <p class="text-xs text-slate-400 mt-1">Mendukung format berkas .xlsx, .xls, .csv, dan .txt</p>
            </div>

            <input type="file" accept=".xlsx, .xls, .csv, .txt" @change="handleFileUpload" class="hidden" id="fileInput" />
            <label for="fileInput" class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-cyan-500/25 transition cursor-pointer flex items-center gap-2">
              <span v-if="isUploading">Membaca Berkas Excel...</span>
              <span v-else>Pilih Berkas Excel / CSV</span>
            </label>
          </div>
        </div>

        <!-- Loaded Data Status & Table Preview -->
        <div v-if="sourceRows.length > 0" class="space-y-4 pt-4 border-t border-slate-800">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span class="text-xs font-bold text-emerald-400">
                Berhasil memuat {{ sourceRows.length }} baris horizontal dengan {{ headers.length }} kolom.
              </span>
            </div>

            <button 
              @click="goToStep2"
              class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/30 transition flex items-center gap-2 cursor-pointer"
            >
              <span>Lanjut ke Mapping Kolom →</span>
            </button>
          </div>

          <!-- Raw Input Preview Table -->
          <div class="overflow-x-auto rounded-xl border border-slate-800 shadow-inner max-h-72">
            <table class="custom-table">
              <thead>
                <tr>
                  <th v-for="header in headers" :key="header">{{ header }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, idx) in sourceRows" :key="idx">
                  <td v-for="header in headers" :key="header" class="font-mono text-xs">
                    {{ row[header] }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Step 2: Column Mapper -->
      <div v-if="currentStep === 2" class="glass-panel p-8 space-y-6">
        <div class="border-b border-slate-800 pb-4">
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 shadow-[0_0_10px_#818cf8]"></span>
            Tahap 2: Pemetaan Kolom Excel ke Matriks Target
          </h3>
          <p class="text-xs text-slate-400 mt-1">Petakan kolom identitas tindakan dan 3 kategori kelompok tarif target.</p>
        </div>

        <!-- Identity Column Mapping Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
          <div>
            <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider mb-2">Nama Tindakan (Action):</label>
            <select v-model="mapping.action_col" class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none">
              <option v-for="h in headers" :key="h" :value="h">{{ h }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider mb-2">Kelas (Class):</label>
            <select v-model="mapping.class_col" class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none">
              <option v-for="h in headers" :key="h" :value="h">{{ h }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider mb-2">Total Tarif (Tariff Umum):</label>
            <select v-model="mapping.total_tariff_col" class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 outline-none">
              <option v-for="h in headers" :key="h" :value="h">{{ h }}</option>
            </select>
          </div>
        </div>

        <!-- 3 Target Category Selector Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Kategori 1: Non-Honor / Jasa RS -->
          <div class="p-5 rounded-2xl bg-slate-900/90 border border-cyan-500/30 space-y-4 shadow-lg shadow-cyan-950/20">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-extrabold text-cyan-400">1. tarif_komponen1</h4>
              <span class="text-[10px] bg-cyan-950 text-cyan-300 font-bold px-2.5 py-1 rounded-md border border-cyan-800/60">Non-Honor / Jasa RS</span>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">Jasa RS, BMHP (Bahan Medis Habis Pakai), Sewa Alat, Akomodasi, Obat.</p>
            <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
              <label v-for="h in availableComponentHeaders" :key="h" class="flex items-center gap-2.5 p-2 rounded-lg bg-slate-950/60 hover:bg-slate-800/60 text-xs text-slate-200 cursor-pointer border border-slate-800/80 transition">
                <input type="checkbox" :value="h" v-model="mapping.cat1_cols" class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-cyan-500 w-4 h-4">
                <span class="font-medium">{{ h }}</span>
              </label>
            </div>
          </div>

          <!-- Kategori 2: Honor Medis / Paramedis -->
          <div class="p-5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-4 shadow-lg shadow-emerald-950/20">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-extrabold text-emerald-400">2. tarif_komponen2</h4>
              <span class="text-[10px] bg-emerald-950 text-emerald-300 font-bold px-2.5 py-1 rounded-md border border-emerald-800/60">Honor Medis</span>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">Operator, Anastesi, Asisten, Penata, Instrumen, Honor Dokter & Paramedis.</p>
            <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
              <label v-for="h in availableComponentHeaders" :key="h" class="flex items-center gap-2.5 p-2 rounded-lg bg-slate-950/60 hover:bg-slate-800/60 text-xs text-slate-200 cursor-pointer border border-slate-800/80 transition">
                <input type="checkbox" :value="h" v-model="mapping.cat2_cols" class="rounded bg-slate-900 border-slate-700 text-emerald-500 focus:ring-emerald-500 w-4 h-4">
                <span class="font-medium">{{ h }}</span>
              </label>
            </div>
          </div>

          <!-- Kategori 3: Jasa Pelayanan -->
          <div class="p-5 rounded-2xl bg-slate-900/90 border border-amber-500/30 space-y-4 shadow-lg shadow-amber-950/20">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-extrabold text-amber-400">3. tarif_komponen3</h4>
              <span class="text-[10px] bg-amber-950 text-amber-300 font-bold px-2.5 py-1 rounded-md border border-amber-800/60">Jasa Pelayanan</span>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">Jasa Pelayanan Umum, Jasa Asuhan Keperawatan, Administrasi.</p>
            <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
              <label v-for="h in availableComponentHeaders" :key="h" class="flex items-center gap-2.5 p-2 rounded-lg bg-slate-950/60 hover:bg-slate-800/60 text-xs text-slate-200 cursor-pointer border border-slate-800/80 transition">
                <input type="checkbox" :value="h" v-model="mapping.cat3_cols" class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500 w-4 h-4">
                <span class="font-medium">{{ h }}</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Additional Options -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
          <h4 class="text-xs font-extrabold text-slate-300 uppercase tracking-wider">Pengaturan Format & Suffix Nama</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-[11px] font-bold text-slate-400 mb-1">Pemisah Komponen Anak:</label>
              <input type="text" v-model="mapping.child_separator" placeholder=" - " class="w-full bg-slate-950 border border-slate-700 rounded-xl p-2.5 text-xs text-white font-mono focus:border-cyan-500 outline-none">
              <p class="text-[10px] text-slate-500 mt-1">Contoh: " - " ➔ Bedah Umum Besar - Operator</p>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-400 mb-1">Akhiran Nama Induk:</label>
              <input type="text" v-model="mapping.parent_suffix" placeholder="*" class="w-full bg-slate-950 border border-slate-700 rounded-xl p-2.5 text-xs text-white font-mono focus:border-cyan-500 outline-none">
              <p class="text-[10px] text-slate-500 mt-1">Contoh: "*" ➔ Bedah Umum Besar*</p>
            </div>
            <div class="flex items-center pt-2">
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="mapping.skip_zero_components" class="rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-cyan-500 w-4 h-4">
                <div>
                  <span class="text-xs font-bold text-slate-200">Abaikan Komponen Rp 0</span>
                  <p class="text-[10px] text-slate-400">Jangan buat baris anak jika tarif Rp 0.</p>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Actions Toolbar -->
        <div class="flex justify-between items-center pt-5 border-t border-slate-800">
          <button @click="currentStep = 1" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition cursor-pointer">
            ← Kembali ke Input Data
          </button>

          <button 
            @click="processConversion" 
            :disabled="isConverting"
            class="px-7 py-3 bg-gradient-to-r from-cyan-500 via-indigo-600 to-indigo-700 hover:from-cyan-400 hover:to-indigo-600 text-white font-extrabold text-xs rounded-xl shadow-xl shadow-cyan-500/25 transition flex items-center gap-2 cursor-pointer disabled:opacity-50"
          >
            <span v-if="isConverting">Memproses Unpivoting Matriks...</span>
            <span v-else>Proses Konversi Matriks Vertikal →</span>
          </button>
        </div>
      </div>

      <!-- Step 3: Data Preview Grid & Validation -->
      <div v-if="currentStep === 3" class="glass-panel p-8 space-y-6">
        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center justify-between">
            <div>
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Baris Output</p>
              <p class="text-2xl font-extrabold text-white mt-1">{{ convertedItems.length }} <span class="text-xs text-slate-500 font-normal">Baris</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center font-bold">📋</div>
          </div>

          <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center justify-between">
            <div>
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Rekonsiliasi</p>
              <p :class="['text-sm font-extrabold mt-1', mismatchCount === 0 ? 'text-emerald-400' : 'text-rose-400']">
                {{ mismatchCount === 0 ? '✓ 100% Presisi Match' : '⚠️ Selisih Terdeteksi' }}
              </p>
            </div>
            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center font-bold', mismatchCount === 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400']">
              {{ mismatchCount === 0 ? '✓' : '⚠️' }}
            </div>
          </div>

          <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center justify-between">
            <div>
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Baris Mismatch</p>
              <p class="text-2xl font-extrabold text-white mt-1">{{ mismatchCount }} <span class="text-xs text-slate-500 font-normal">Baris</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold">⚖️</div>
          </div>
        </div>

        <!-- Converted Output Data Grid -->
        <div class="overflow-x-auto rounded-2xl border border-slate-800 shadow-2xl max-h-[500px]">
          <table class="custom-table">
            <thead>
              <tr>
                <th class="w-12 text-center">No</th>
                <th>nama tindakan</th>
                <th>kelas</th>
                <th class="text-center">induk</th>
                <th class="text-right">total tarif</th>
                <th class="text-right">tarif_komponen1</th>
                <th class="text-right">tarif_komponen2</th>
                <th class="text-right">tarif_komponen3</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="item in convertedItems" 
                :key="item.row_index"
                :class="[
                  item.induk ? 'row-parent' : 'row-child',
                  item.has_mismatch ? 'row-mismatch' : ''
                ]"
              >
                <td class="text-center font-mono text-xs opacity-70">{{ item.row_index }}</td>
                <td class="font-semibold">{{ item.nama_tindakan }}</td>
                <td><span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-800 text-slate-300 border border-slate-700">{{ item.kelas }}</span></td>
                <td class="text-center">
                  <span :class="item.induk ? 'badge-parent' : 'badge-child'">
                    <span v-if="item.induk">★</span>
                    {{ item.induk ? 'true' : 'false' }}
                  </span>
                </td>
                <td class="font-mono text-right font-bold text-white">{{ formatCurrency(item.total_tarif) }}</td>
                <td class="font-mono text-right font-semibold text-cyan-400">{{ formatCurrency(item.tarif_komponen1) }}</td>
                <td class="font-mono text-right font-semibold text-emerald-400">{{ formatCurrency(item.tarif_komponen2) }}</td>
                <td class="font-mono text-right font-semibold text-amber-400">{{ formatCurrency(item.tarif_komponen3) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Navigation Bar -->
        <div class="flex justify-between items-center pt-4 border-t border-slate-800">
          <button @click="currentStep = 2" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition cursor-pointer">
            ← Ubah Mapping Kolom
          </button>

          <button @click="currentStep = 4" class="px-7 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-xl shadow-emerald-600/30 transition flex items-center gap-2 cursor-pointer">
            <span>Lanjut ke Export & Download →</span>
          </button>
        </div>
      </div>

      <!-- Step 4: Export Options -->
      <div v-if="currentStep === 4" class="glass-panel p-10 space-y-8 text-center max-w-2xl mx-auto border border-slate-800">
        <div class="w-20 h-20 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto text-3xl shadow-xl">
          ✓
        </div>

        <div class="space-y-2">
          <h3 class="text-2xl font-extrabold text-white">Konversi Berhasil Diselesaikan!</h3>
          <p class="text-sm text-slate-400">
            Sebanyak <span class="text-emerald-400 font-bold">{{ convertedItems.length }} baris</span> data tarif hirarki telah siap diunduh dan di-import ke SIMRS.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
          <button 
            @click="downloadCsv"
            class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-xl shadow-cyan-500/30 transition flex items-center justify-center gap-2 cursor-pointer"
          >
            <span>📥 Unduh Format CSV (.csv)</span>
          </button>

          <button 
            @click="copyToClipboard"
            class="w-full sm:w-auto px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl border border-slate-700 transition flex items-center justify-center gap-2 cursor-pointer shadow-inner"
          >
            <span>{{ copied ? '✓ Berhasil Disalin!' : '📋 Salin ke Clipboard' }}</span>
          </button>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const currentStep = ref(1)
const isConverting = ref(false)
const isUploading = ref(false)
const copied = ref(false)

const headers = ref([])
const sourceRows = ref([])
const convertedItems = ref([])
const mismatchCount = ref(0)

const mapping = ref({
  action_col: 'nama tindakan',
  class_col: 'kelas',
  total_tariff_col: 'tariff umum',
  cat1_cols: ['jasa rs'],
  cat2_cols: ['operator', 'asisten', 'anastesi', 'penata', 'instrumen'],
  cat3_cols: ['jasa pelayanan'],
  parent_suffix: '*',
  child_separator: ' - ',
  skip_zero_components: true,
})

const hasData = computed(() => sourceRows.value.length > 0)
const isMapped = computed(() => mapping.value.action_col && mapping.value.class_col)
const isConverted = computed(() => convertedItems.value.length > 0)

const availableComponentHeaders = computed(() => {
  return headers.value.filter(h => 
    h !== mapping.value.action_col && 
    h !== mapping.value.class_col && 
    h !== mapping.value.total_tariff_col
  )
})

// Handle Excel / CSV File Upload (.xlsx, .xls, .csv, .txt)
const handleFileUpload = async (e) => {
  const file = e.target.files[0]
  if (!file) return

  isUploading.value = true
  try {
    const formData = new FormData()
    formData.append('file', file)

    const res = await fetch('/api/upload', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
      },
      body: formData,
    })

    const json = await res.json()
    if (res.ok && json.success) {
      headers.value = json.data.headers
      sourceRows.value = json.data.rows
      autoMatchMapping(json.data.headers)
    } else {
      alert(json.message || 'Gagal membaca berkas Excel. Pastikan format .xlsx, .xls, atau .csv valid.')
    }
  } catch (err) {
    console.error(err)
    alert('Terjadi kesalahan saat mengunggah berkas: ' + err.message)
  } finally {
    isUploading.value = false
  }
}

// Auto-match headers to mapping categories
const autoMatchMapping = (hList) => {
  const lowerList = hList.map(h => h.toLowerCase())

  const findHeader = (patterns) => {
    const idx = lowerList.findIndex(h => patterns.some(p => h.includes(p)))
    return idx !== -1 ? hList[idx] : null
  }

  const action = findHeader(['nama tindakan', 'tindakan', 'action', 'procedure'])
  if (action) mapping.value.action_col = action

  const kelas = findHeader(['kelas', 'class'])
  if (kelas) mapping.value.class_col = kelas

  const total = findHeader(['tariff umum', 'total tarif', 'tarif umum', 'total', 'umum'])
  if (total) mapping.value.total_tariff_col = total

  // Match categories
  const cat1 = []
  const cat2 = []
  const cat3 = []

  const ignoredKeywords = ['no', 'nomor', 'kode', 'nama tarif', 'kategori', 'keterangan', 'tipe', 'group']

  hList.forEach(h => {
    const l = h.toLowerCase()
    if (
      l === mapping.value.action_col?.toLowerCase() || 
      l === mapping.value.class_col?.toLowerCase() || 
      l === mapping.value.total_tariff_col?.toLowerCase() ||
      ignoredKeywords.some(kw => l === kw || l.includes('nama tarif') || l.includes('kode'))
    ) {
      return
    }

    if (l.includes('jasa rs') || l.includes('bmhp') || l.includes('fasilitas') || l.includes('sewa') || l.includes('sarana') || l.includes('obat')) {
      cat1.push(h)
    } else if (l.includes('operator') || l.includes('asisten') || l.includes('anastesi') || l.includes('penata') || l.includes('instrumen') || l.includes('honor') || l.includes('dokter') || l.includes('medis')) {
      cat2.push(h)
    } else if (l.includes('pelayanan') || l.includes('jasa pelayanan') || l.includes('keperawatan') || l.includes('admin')) {
      cat3.push(h)
    }
  })

  mapping.value.cat1_cols = cat1
  mapping.value.cat2_cols = cat2
  mapping.value.cat3_cols = cat3
}

// Load preloaded sample data matching user's exact specification
const loadSampleData = () => {
  headers.value = ['nama tindakan', 'kelas', 'tariff umum', 'operator', 'asisten', 'anastesi', 'penata', 'instrumen', 'jasa rs', 'jasa pelayanan']
  
  sourceRows.value = [
    {
      'nama tindakan': 'Laparascopy',
      'kelas': 'KELAS 1',
      'tariff umum': '1.000.000',
      'operator': '500.000',
      'asisten': '50.000',
      'anastesi': '50.000',
      'penata': '50.000',
      'instrumen': '50.000',
      'jasa rs': '150.000',
      'jasa pelayanan': '50.000',
    },
    {
      'nama tindakan': 'Laparascopy',
      'kelas': 'KELAS 2',
      'tariff umum': '2.000.000',
      'operator': '1.000.000',
      'asisten': '100.000',
      'anastesi': '100.000',
      'penata': '100.000',
      'instrumen': '100.000',
      'jasa rs': '450.000',
      'jasa pelayanan': '50.000',
    }
  ]

  autoMatchMapping(headers.value)
}

const goToStep2 = () => {
  currentStep.value = 2
}

// Execute unpivoting conversion matrix
const processConversion = async () => {
  if (!mapping.value.action_col || !mapping.value.class_col) {
    alert('Harap pilih kolom Nama Tindakan dan Kelas terlebih dahulu!')
    return
  }

  isConverting.value = true
  try {
    const res = await fetch('/api/convert', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        rows: sourceRows.value,
        mapping: mapping.value,
      }),
    })

    const json = await res.json()
    if (res.ok && json.success) {
      convertedItems.value = json.data.items
      mismatchCount.value = json.data.mismatch_count
      currentStep.value = 3
    } else {
      alert(json.message || 'Gagal memproses konversi matriks. Periksa kembali konfigurasi mapping kolom Anda.')
    }
  } catch (err) {
    console.error(err)
    alert('Terjadi kesalahan koneksi server: ' + err.message)
  } finally {
    isConverting.value = false
  }
}

const formatCurrency = (val) => {
  if (val === 0 || !val) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
}

const downloadCsv = async () => {
  const res = await fetch('/api/export', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ items: convertedItems.value }),
  })
  const blob = await res.blob()
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `konversi_tarif_rs_${Date.now()}.csv`
  a.click()
}

const copyToClipboard = () => {
  let csv = 'nama tindakan\tkelas\tinduk\ttotal tarif\ttarif_komponen1\ttarif_komponen2\ttarif_komponen3\n'
  convertedItems.value.forEach(item => {
    csv += `${item.nama_tindakan}\t${item.kelas}\t${item.induk ? 'true' : 'false'}\t${item.total_tarif}\t${item.tarif_komponen1}\t${item.tarif_komponen2}\t${item.tarif_komponen3}\n`
  })
  navigator.clipboard.writeText(csv)
  copied.value = true
  setTimeout(() => copied.value = false, 3000)
}
</script>
