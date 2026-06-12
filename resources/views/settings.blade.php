@extends('layouts.dashboard')

@section('title', 'Settings - Configure Erid')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12 w-full space-y-8">
    <h1 class="text-3xl font-extrabold font-['Orbitron'] text-[color:var(--text-main)] border-b border-[color:var(--border-color)] pb-4">
        Pengaturan Lab
    </h1>

    @if(session('status') === 'password-updated')
        <div class="p-4 bg-green-950/50 border border-green-500 text-green-200 rounded-xl text-sm">
            "Rocky fix password! Password secure now!"
        </div>
    @endif

    @if(session('status') === 'theme-updated')
        <div class="p-4 bg-green-950/50 border border-green-500 text-green-200 rounded-xl text-sm">
            "Rocky switch control screen! It looks beautiful!"
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Theme Switcher Card -->
        <div class="bg-[color:var(--bg-card)] border border-[color:var(--border-color)] rounded-2xl p-6 space-y-4">
            <h2 class="text-xl font-bold font-['Orbitron'] text-[color:var(--primary)] flex items-center space-x-2">
                <span>🎨</span> <span>Tema Visual</span>
            </h2>
            <p class="text-xs text-[color:var(--text-muted)]">"Choose screen layout, friend! Many stars!"</p>

            <form action="{{ route('settings.theme') }}" method="POST" id="theme-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <!-- Adrian Theme Option -->
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_preference" value="adrian" class="sr-only" 
                               {{ auth()->user()->theme_preference === 'adrian' ? 'checked' : '' }}
                               onchange="document.getElementById('theme-form').submit()">
                        <div class="p-3 border-2 rounded-xl text-center font-bold text-xs bg-[#050F0B] text-[#E6F4EA] transition
                                    {{ auth()->user()->theme_preference === 'adrian' ? 'border-[#00FF9D]' : 'border-[#1A3025]' }}">
                            Adrian (Default)
                        </div>
                    </label>

                    <!-- Petrova Theme Option -->
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_preference" value="petrova" class="sr-only"
                               {{ auth()->user()->theme_preference === 'petrova' ? 'checked' : '' }}
                               onchange="document.getElementById('theme-form').submit()">
                        <div class="p-3 border-2 rounded-xl text-center font-bold text-xs bg-[#120A21] text-[#F4F1FA] transition
                                    {{ auth()->user()->theme_preference === 'petrova' ? 'border-[#FF2E93]' : 'border-[#3C1E5C]' }}">
                            Petrova (Magenta)
                        </div>
                    </label>

                    <!-- Dark Mode Option -->
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_preference" value="dark" class="sr-only"
                               {{ auth()->user()->theme_preference === 'dark' ? 'checked' : '' }}
                               onchange="document.getElementById('theme-form').submit()">
                        <div class="p-3 border-2 rounded-xl text-center font-bold text-xs bg-[#030712] text-[#E5E7EB] transition
                                    {{ auth()->user()->theme_preference === 'dark' ? 'border-[#F59E0B]' : 'border-[#374151]' }}">
                            Classic Dark
                        </div>
                    </label>

                    <!-- Light Mode Option -->
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_preference" value="light" class="sr-only"
                               {{ auth()->user()->theme_preference === 'light' ? 'checked' : '' }}
                               onchange="document.getElementById('theme-form').submit()">
                        <div class="p-3 border-2 rounded-xl text-center font-bold text-xs bg-[#F8FAFC] text-[#1E293B] transition
                                    {{ auth()->user()->theme_preference === 'light' ? 'border-[#4F46E5]' : 'border-[#E2E8F0]' }}">
                            Classic Light
                        </div>
                    </label>
                </div>
            </form>
        </div>

        <!-- Password Change Card -->
        <div class="bg-[color:var(--bg-card)] border border-[color:var(--border-color)] rounded-2xl p-6 space-y-4">
            <h2 class="text-xl font-bold font-['Orbitron'] text-[color:var(--primary)] flex items-center space-x-2">
                <span>🔑</span> <span>Ganti Password</span>
            </h2>
            <p class="text-xs text-[color:var(--text-muted)]">"Switch locks key, friend! Prevent Erid intrusion!"</p>

            @if($errors->any())
                <div class="p-3 bg-red-950/50 border border-red-500 text-red-200 text-xs rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('settings.password') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label for="current_password" class="block text-xs font-semibold mb-1 text-[color:var(--text-main)]">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" required
                           class="w-full px-3 py-2 text-sm bg-[color:var(--bg-main)] border border-[color:var(--border-color)] rounded-xl focus:border-[color:var(--primary)] focus:outline-none text-[color:var(--text-main)] transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold mb-1 text-[color:var(--text-main)]">Password Baru</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-3 py-2 text-sm bg-[color:var(--bg-main)] border border-[color:var(--border-color)] rounded-xl focus:border-[color:var(--primary)] focus:outline-none text-[color:var(--text-main)] transition">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold mb-1 text-[color:var(--text-main)]">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-3 py-2 text-sm bg-[color:var(--bg-main)] border border-[color:var(--border-color)] rounded-xl focus:border-[color:var(--primary)] focus:outline-none text-[color:var(--text-main)] transition">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl btn-semi-3d text-sm mt-2 cursor-pointer">
                    Perbarui Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
