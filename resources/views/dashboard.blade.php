@extends('layouts.dashboard')

@section('title', 'Dashboard - Learn with Rocky')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Welcome Header -->
    <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 md:p-8 shadow-[0_0_20px_var(--border-glow)] flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold font-['Orbitron'] text-(--text-main)">
                Welcome back, <span class="text-(--primary)">{{ auth()->user()->username }}</span>!
            </h1>
            <p class="text-(--text-muted) mt-2">
                "Hello, friend! Ready to learn new things today? Question?"
            </p>
        </div>
        <a href="{{ route('notes.index') }}" class="px-6 py-3.5 rounded-xl btn-semi-3d text-sm font-bold uppercase tracking-wider cursor-pointer">
            Open Notebooks! 📓
        </a>
    </div>

    <!-- Gamification Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Stat: Notes -->
        <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 flex items-center space-x-4">
            <span class="text-4xl">📓</span>
            <div>
                <p class="text-2xl font-bold font-['Orbitron'] text-(--primary)">
                    {{ auth()->user()->notes()->count() }}
                </p>
                <p class="text-xs text-(--text-muted) font-semibold uppercase tracking-wider">Notes Created</p>
            </div>
        </div>

        <!-- Stat: Quizzes Created -->
        <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 flex items-center space-x-4">
            <span class="text-4xl">🧪</span>
            <div>
                <p class="text-2xl font-bold font-['Orbitron'] text-(--primary)">
                    {{ auth()->user()->quizzes()->count() }}
                </p>
                <p class="text-xs text-(--text-muted) font-semibold uppercase tracking-wider">Quizzes Created</p>
            </div>
        </div>

        <!-- Stat: Quizzes Answered -->
        <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 flex items-center space-x-4">
            <span class="text-4xl">✅</span>
            <div>
                <p class="text-2xl font-bold font-['Orbitron'] text-(--primary)">
                    {{ auth()->user()->stats->total_quizzes_answered ?? 0 }}
                </p>
                <p class="text-xs text-(--text-muted) font-semibold uppercase tracking-wider">Quizzes Answered</p>
            </div>
        </div>
    </div>

    <!-- Accuracy Ratio & Gamification Detail -->
    <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 md:p-8 space-y-6">
        <h2 class="text-xl font-bold font-['Orbitron'] text-(--primary)">
            🎯 Learning Accuracy Ratio
        </h2>

        @php
            $answered = auth()->user()->stats->total_quizzes_answered ?? 0;
            $correct = auth()->user()->stats->correct_answers ?? 0;
            $incorrect = auth()->user()->stats->incorrect_answers ?? 0;
            $totalQuestions = $correct + $incorrect;
            $accuracy = $totalQuestions > 0 ? round(($correct / $totalQuestions) * 100) : 0;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
            <!-- Details -->
            <div class="space-y-3 md:col-span-1">
                <div class="flex justify-between text-sm">
                    <span class="text-(--text-muted)">Correct Answers:</span>
                    <span class="font-bold text-green-400">{{ $correct }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-(--text-muted)">Incorrect Answers:</span>
                    <span class="font-bold text-red-400">{{ $incorrect }}</span>
                </div>
                <div class="flex justify-between text-sm border-t border-(--border-color) pt-2">
                    <span class="text-(--text-muted)">Accuracy Percentage:</span>
                    <span class="font-bold text-(--primary) text-lg">{{ $accuracy }}%</span>
                </div>
            </div>

            <!-- Visual Progress Bar -->
            <div class="md:col-span-2 space-y-2">
                <div class="w-full bg-(--bg-main) rounded-full h-6 p-1 border border-(--border-color) flex overflow-hidden">
                    @if($totalQuestions > 0)
                        <div class="bg-green-400 h-full rounded-full transition-all duration-500" style="width: {{ ($correct / $totalQuestions) * 100 }}%"></div>
                        <div class="bg-red-400 h-full rounded-full transition-all duration-500" style="width: {{ ($incorrect / $totalQuestions) * 100 }}%"></div>
                    @else
                        <div class="text-[10px] text-(--text-muted) mx-auto self-center font-bold">No quizzes answered yet!</div>
                    @endif
                </div>
                <div class="flex justify-between text-xs text-(--text-muted) font-mono">
                    <span>Correct (Green)</span>
                    <span>Incorrect (Red)</span>
                </div>
            </div>
        </div>

        @if($accuracy >= 80 && $totalQuestions > 0)
            <div class="p-4 bg-green-950/30 border border-green-500/50 rounded-xl flex items-center space-x-3 text-sm text-green-300">
                <span class="text-2xl">✨</span>
                <p><strong>"Amaze! Amaze! Amaze! Accuracy super high! Fist bump, friend! Keep studying!"</strong></p>
            </div>
        @elseif($totalQuestions > 0)
            <div class="p-4 bg-(--bg-main) border border-(--border-color) rounded-xl flex items-center space-x-3 text-sm text-(--text-muted)">
                <span class="text-2xl">💪</span>
                <p><strong>"No worry, friend! Practice makes perfect. Words of encouragement!"</strong></p>
            </div>
        @endif
    </div>
</div>
@endsection
