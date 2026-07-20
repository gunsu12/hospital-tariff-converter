<template>
  <div class="min-h-screen bg-[#0b0f19] text-slate-100 flex flex-col selection:bg-cyan-500 selection:text-white">
    <!-- Top Glow Gradient Line -->
    <div class="h-1 w-full bg-gradient-to-r from-cyan-500 via-indigo-500 to-emerald-500"></div>

    <!-- Header Navbar -->
    <header class="border-b border-slate-800/80 bg-[#0f172a]/80 backdrop-blur-xl sticky top-0 z-50 shadow-2xl">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <!-- Logo & Title -->
        <div class="flex items-center space-x-4">
          <div class="relative group">
            <div class="absolute -inset-1 rounded-2xl bg-gradient-to-r from-cyan-500 to-indigo-600 blur opacity-70 group-hover:opacity-100 transition duration-300"></div>
            <div class="relative w-12 h-12 rounded-xl bg-slate-950 border border-slate-700/60 flex items-center justify-center font-extrabold text-white text-xl shadow-xl">
              <span class="bg-gradient-to-tr from-cyan-400 to-indigo-400 bg-clip-text text-transparent">HT</span>
            </div>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h1 class="font-extrabold text-xl text-white tracking-tight">Hospital Tariff Converter</h1>
              <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-md bg-cyan-500/10 text-cyan-400 border border-cyan-500/30">v1.0 Pro</span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Unpivoting Horizontal Excel Master Tariff into Vertical SIMRS Format</p>
          </div>
        </div>

        <!-- Tech Stack Pill Badges -->
        <div class="hidden md:flex items-center space-x-3 text-xs">
          <div class="px-3.5 py-1.5 rounded-full bg-slate-900 border border-slate-800 flex items-center gap-2 shadow-inner">
            <span class="w-2 h-2 rounded-full bg-red-500"></span>
            <span class="font-semibold text-slate-300">Laravel 13 + Inertia</span>
          </div>
          <div class="px-3.5 py-1.5 rounded-full bg-slate-900 border border-slate-800 flex items-center gap-2 shadow-inner">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span class="font-semibold text-slate-300">Vue 3</span>
          </div>
          <div class="px-3.5 py-1.5 rounded-full bg-cyan-950/60 border border-cyan-800/60 text-cyan-300 flex items-center gap-2 shadow-lg shadow-cyan-950/50">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
            <span class="font-bold">PostgreSQL 18</span>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-[#090d16] py-8 text-center text-xs text-slate-500 space-y-2">
      <p class="font-medium">Hospital Tariff Matrix Converter System &copy; 2026</p>
      <p class="text-[11px] text-slate-600">Built with Laravel 13, Inertia.js v2, Vue 3, and PostgreSQL 18</p>
    </footer>
  </div>
</template>

<script setup>
</script>
