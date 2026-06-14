<!DOCTYPE html>
<html lang="en" class="theme-{{ auth()->check() ? auth()->user()->theme_preference : 'adrian' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Learn with Rocky')</title>
    <!-- Google Fonts: Outfit & Orbitron (Sci-Fi Theme) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen">
    <!-- Cosmic background overlay -->
    <div class="fixed inset-0 pointer-events-none opacity-20 bg-[radial-gradient(ellipse_at_center,var(--tw-gradient-stops))] from-emerald-500/20 via-transparent to-transparent z-0"></div>

    <header class="w-full py-4 px-6 border-b border-(--border-color) bg-(--bg-card)/50 backdrop-blur-md sticky top-0 z-50 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <span class="font-['Orbitron'] text-xl font-black tracking-wider text-(--primary)">LEARN WITH ROCKY</span>
        </div>
        <nav class="flex items-center space-x-4">
            @auth
                <a href="{{ route('dashboard') }}" class="text-(--text-main) hover:text-(--primary) transition">Dashboard</a>
                <a href="{{ route('settings') }}" class="text-(--text-main) hover:text-(--primary) transition">Settings</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300 transition cursor-pointer">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-(--text-main) hover:text-(--primary) transition">Login</a>
                <a href="{{ route('register') }}" class="px-4 py-1.5 rounded-lg border border-(--primary) text-(--primary) hover:bg-(--primary) hover:text-black transition">Register</a>
            @endauth
        </nav>
    </header>

    <main class="grow flex flex-col relative z-10">
        @yield('content')
    </main>

    <footer class="w-full py-4 text-center border-t border-(--border-color) bg-(--bg-card)/30 text-xs text-(--text-muted) z-10">
        © 2026 Learn With Rocky
    </footer>
</body>
</html>
