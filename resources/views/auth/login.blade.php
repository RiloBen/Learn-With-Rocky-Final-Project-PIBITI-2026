@extends('layouts.app')

@section('title', 'Login - Back to Study!')

@section('content')
<div class="flex-grow flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-[color:var(--bg-card)] border-2 border-[color:var(--border-color)] rounded-2xl p-8 shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
        <div class="text-center mb-6">
            <span class="text-3xl">🚀</span>
            <h2 class="text-2xl font-bold font-['Orbitron'] text-[color:var(--text-main)] mt-2">Masuk ke Lab</h2>
            <p class="text-xs text-[color:var(--text-muted)] mt-1">"Login username, friend!"</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-950/50 border border-red-500 text-red-200 text-sm rounded-lg">
                <p class="font-bold">Apology!</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-sm font-semibold mb-1 text-[color:var(--text-main)]">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required
                       class="w-full px-4 py-2.5 bg-[color:var(--bg-main)] border border-[color:var(--border-color)] rounded-xl focus:border-[color:var(--primary)] focus:outline-none text-[color:var(--text-main)] transition">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold mb-1 text-[color:var(--text-main)]">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-2.5 bg-[color:var(--bg-main)] border border-[color:var(--border-color)] rounded-xl focus:border-[color:var(--primary)] focus:outline-none text-[color:var(--text-main)] transition">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl btn-semi-3d mt-4 cursor-pointer">
                Login! Amaze!
            </button>
        </form>

        <p class="text-center text-sm text-[color:var(--text-muted)] mt-6">
            Belum terdaftar? <a href="{{ route('register') }}" class="text-[color:var(--primary)] hover:underline">Buat akun baru</a>
        </p>
    </div>
</div>
@endsection
