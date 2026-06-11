@extends('layouts.app')

@section('title', 'Dashboard - Learn with Rocky')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12 w-full space-y-6">
    <div class="bg-[color:var(--bg-card)] border border-[color:var(--border-color)] rounded-2xl p-8 shadow-[0_0_30px_var(--border-glow)] relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-[color:var(--primary)] opacity-5 rounded-full blur-3xl pointer-events-none"></div>

        <h1 class="text-3xl font-extrabold font-['Orbitron'] text-[color:var(--text-main)]">
            Selamat belajar kembali, <span class="text-[color:var(--primary)]">{{ auth()->user()->username }}</span>!
        </h1>
        <p class="text-[color:var(--text-muted)] mt-2">
            Selamat datang di Pusat Kendali Belajar Rocky. Ini adalah basis eksperimen Anda!
        </p>

        <div class="mt-8 border-t border-[color:var(--border-color)] pt-6 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-3 mb-4 md:mb-0">
                <span class="text-3xl">🕷️</span>
                <div>
                    <p class="text-sm font-semibold text-[color:var(--text-main)]">"Rocky is calculating notes..."</p>
                    <p class="text-xs text-[color:var(--text-muted)]">Modul Notebook & Kuis menyusul di Milestone berikutnya!</p>
                </div>
            </div>
            <a href="{{ route('settings') }}" class="px-5 py-2.5 rounded-xl btn-semi-3d text-sm inline-block text-center">
                Buka Pengaturan
            </a>
        </div>
    </div>
</div>
@endsection
