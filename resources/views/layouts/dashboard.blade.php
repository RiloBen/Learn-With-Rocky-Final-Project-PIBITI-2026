<!DOCTYPE html>
<html lang="id" class="theme-{{ auth()->check() ? auth()->user()->theme_preference : 'adrian' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Learn with Rocky')</title>
    <!-- Google Fonts: Outfit & Orbitron -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[color:var(--bg-main)] text-[color:var(--text-main)] flex min-h-screen relative overflow-x-hidden">
    <!-- Starfield/Glow effect -->
    <div class="fixed inset-0 pointer-events-none opacity-20 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-emerald-500/20 via-transparent to-transparent z-0"></div>

    <!-- MOBILE HEADER -->
    <div class="md:hidden w-full flex items-center justify-between px-4 py-3 bg-[color:var(--bg-card)] border-b border-[color:var(--border-color)] fixed top-0 left-0 right-0 z-40">
        <button id="mobile-menu-toggle" class="text-[color:var(--text-main)] hover:text-[color:var(--primary)] focus:outline-none p-1 cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <span class="font-['Orbitron'] text-md font-black tracking-wider text-[color:var(--primary)]">ROCKY LAB</span>
        <div class="w-6"></div> <!-- spacer -->
    </div>

    <!-- SIDEBAR (Desktop: Left Fixed | Mobile: Off-canvas Overlay) -->
    <aside id="sidebar" class="w-64 bg-[color:var(--bg-card)] border-r border-[color:var(--border-color)] flex flex-col fixed inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:sticky md:h-screen">
        <!-- Brand/Header -->
        <div class="p-6 border-b border-[color:var(--border-color)] flex items-center space-x-2">
            <span class="text-2xl animate-pulse">🕷️</span>
            <span class="font-['Orbitron'] font-black tracking-wider text-lg text-[color:var(--primary)]">ROCKY LAB</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition font-semibold {{ request()->routeIs('dashboard') ? 'bg-[color:var(--primary)] text-black font-bold shadow-[0_0_10px_var(--border-glow)]' : 'hover:bg-[color:var(--bg-main)] hover:text-[color:var(--primary)]' }}">
                <span class="text-lg">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('notes.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition font-semibold {{ request()->routeIs('notes.*') ? 'bg-[color:var(--primary)] text-black font-bold shadow-[0_0_10px_var(--border-glow)]' : 'hover:bg-[color:var(--bg-main)] hover:text-[color:var(--primary)]' }}">
                <span class="text-lg">📓</span>
                <span>Notebooks</span>
            </a>
            <a href="{{ route('settings') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition font-semibold {{ request()->routeIs('settings') ? 'bg-[color:var(--primary)] text-black font-bold shadow-[0_0_10px_var(--border-glow)]' : 'hover:bg-[color:var(--bg-main)] hover:text-[color:var(--primary)]' }}">
                <span class="text-lg">⚙️</span>
                <span>Settings</span>
            </a>
        </nav>

        <!-- User profile & Logout -->
        <div class="p-4 border-t border-[color:var(--border-color)] bg-[color:var(--bg-main)]/50 space-y-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full border-2 border-[color:var(--primary)] flex items-center justify-center font-bold font-mono bg-[color:var(--bg-card)]">
                    {{ strtoupper(substr(auth()->user()->username, 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate">{{ auth()->user()->username }}</p>
                    <p class="text-[10px] text-[color:var(--text-muted)] truncate">Level: Science Friend</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="block w-full">
                @csrf
                <button type="submit" class="w-full py-2 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white transition rounded-xl font-bold text-xs cursor-pointer">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile sidebar overlay backdrop -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 hidden transition-opacity duration-300 md:hidden"></div>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="flex-grow flex flex-col min-w-0 pt-12 md:pt-0">
        <main class="flex-grow p-6 md:p-8 relative z-10">
            @yield('content')
        </main>
        
        <footer class="w-full py-4 text-center border-t border-[color:var(--border-color)] bg-[color:var(--bg-card)]/30 text-xs text-[color:var(--text-muted)] mt-auto relative z-10">
            © 2026 Learn With Rocky. Made with 💚. Fist bump! 🐾
        </footer>
    </div>

    <!-- JS script for Mobile Burger Menu -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleMenu() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            if(toggleBtn) toggleBtn.addEventListener('click', toggleMenu);
            if(overlay) overlay.addEventListener('click', toggleMenu);
        });
    </script>
</body>
</html>
