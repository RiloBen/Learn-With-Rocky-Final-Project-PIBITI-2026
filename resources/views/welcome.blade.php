@extends('layouts.app')

@section('title', 'Welcome to Learn with Rocky! Amaze!')

@section('content')
<div class="grow flex flex-col items-center justify-center px-6 py-12 text-center max-w-4xl mx-auto relative">
    <!-- Starfield CSS effect -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

    <!-- Rocky Avatar & Dialogue bubble -->
    <div class="flex flex-col items-center mb-8 relative">
        <!-- Dialog Bubble -->
        <div class="relative bg-(--bg-card) border-2 border-(--primary) rounded-2xl p-4 max-w-sm mb-6 shadow-[0_0_15px_var(--border-glow)]">
            <p class="font-mono text-sm leading-relaxed text-(--text-main)">
                "Hello, friend! You want study science? Question? Rocky help summarizes & makes quiz!"
            </p>
            <div class="absolute bottom-[-10px] left-1/2 transform -translate-x-1/2 w-4 h-4 bg-(--bg-card) border-r-2 border-b-2 border-(--primary) rotate-45"></div>
        </div>
        
        <!-- Avatar Placeholder with Sci-Fi style -->
        <div class="w-50 h-50 rounded-full border-4 border-(--primary) bg-(--bg-card) flex items-center justify-center shadow-[0_0_20px_var(--border-glow)] animate-pulse">
            <!-- <span class="text-4xl">🚀</span> -->
            <img src="{{ asset('favicon_io/android-chrome-512x512.png') }}" class="w-50 h-50 mx-auto">
        </div>
    </div>

    <h1 class="text-4xl md:text-6xl font-extrabold font-['Orbitron'] tracking-tight mb-4 text-(--text-main)">
        Learn With <span class="text-(--primary)">Rocky</span>
    </h1>
    
    <p class="text-lg md:text-xl text-(--text-muted) max-w-xl mb-8">
        Upload your PDF document, let Rocky analyze and create structured notes and interactive quizzes without hallucinations!
    </p>

    <div>
        @auth
            <a href="{{ route('dashboard') }}" class="px-8 py-4 rounded-xl text-lg font-bold btn-semi-3d uppercase tracking-wider inline-block">
                To Dashboard!
            </a>
        @else
            <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl text-lg font-bold btn-semi-3d uppercase tracking-wider inline-block">
                Start Learning!
            </a>
        @endauth
    </div>
</div>
@endsection
