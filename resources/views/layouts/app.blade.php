<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QR Attendance') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @vite(['resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen bg-slate-50">
            @unless(View::hasSection('showNavigation') && trim(View::getSection('showNavigation')) === 'false')
                @php
                    $user = Auth::user();
                    $isSuperAdmin = $user?->role === \App\Models\User::ROLE_SUPERADMIN;
                    $isTeacher = $user?->role === \App\Models\User::ROLE_TEACHER;
                @endphp

                <div class="flex min-h-screen flex-col lg:flex-row">
                    <!-- Sidebar - Mobile Responsive -->
                    <aside class="w-full lg:w-72 xl:w-80 border-b lg:border-b-0 lg:border-r border-slate-200/80 bg-slate-950 text-slate-100">
                        <!-- Logo Section -->
                        <div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-4 sm:px-6 sm:py-5">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl sm:rounded-2xl bg-white/10 shrink-0">
                                    <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M8 10h8" stroke-linecap="round" />
                                        <path d="M8 14h5" stroke-linecap="round" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-semibold truncate">{{ config('app.name', 'QR Attendance') }}</div>
                                    <div class="text-xs text-slate-400 truncate">Admin workspace</div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Menu -->
                        <nav class="space-y-1 px-3 py-4 sm:px-4 sm:py-5 overflow-y-auto max-h-[calc(100vh-150px)] lg:max-h-[calc(100vh-100px)]">
                            @if($isSuperAdmin)
                                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span class="text-base">◉</span>
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ route('superadmin.teachers.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('superadmin.teachers.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span class="text-base">◉</span>
                                    <span>Teachers</span>
                                </a>

                            @elseif($isTeacher)
                                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.dashboard') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span class="text-base">◉</span>
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ route('teacher.classes.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.classes.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span class="text-base">◉</span>
                                    <span>Classes</span>
                                </a>
                                <a href="{{ route('teacher.students.all') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.students.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span class="text-base">◉</span>
                                    <span>Students</span>
                                </a>
                                <a href="{{ route('teacher.attendance.records') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.attendance.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span class="text-base">◉</span>
                                    <span>Records</span>
                                </a>
                                <a href="{{ route('teacher.recommendations') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.recommendations') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span class="text-base">◉</span>
                                    <span>Recommendations</span>
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition text-slate-300 hover:bg-white/10 hover:text-white">
                                    <span class="text-base">◉</span>
                                    <span>Dashboard</span>
                                </a>
                            @endif
                        </nav>
                    </aside>

                    <div class="flex flex-1 flex-col">
                        <!-- Header - Mobile Optimized -->
                        <header class="border-b border-slate-200/80 bg-white/90 backdrop-blur sticky top-0 z-40">
                            <div class="flex flex-col gap-3 px-3 py-3 sm:px-6 sm:py-4 lg:px-8 lg:flex-row lg:items-center lg:justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-semibold text-slate-900">@yield('pageTitle', 'Dashboard')</p>
                                    <p class="text-xs text-slate-500">@yield('pageSubtitle', 'Welcome back')</p>
                                </div>

                                <div class="flex items-center gap-2 sm:gap-3">
                                    <a href="{{ route('teacher.notifications') }}" class="rounded-lg border border-slate-200 bg-white p-2 text-slate-600 shadow-sm hover:border-slate-300 hover:bg-slate-50 transition text-base">🔔</a>

                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 sm:gap-3 rounded-lg border border-slate-200 bg-slate-50 px-2 sm:px-3 py-2 transition hover:border-slate-300 hover:bg-slate-100" aria-label="Edit profile" title="Edit profile">
                                        <div class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-slate-900 text-xs sm:text-sm font-semibold text-white shrink-0">
                                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="hidden sm:block min-w-0">
                                            <div class="text-xs sm:text-sm font-semibold text-slate-900 truncate">{{ $user?->name ?? 'Guest' }}</div>
                                            <div class="text-xs text-slate-500 truncate">{{ $user?->email ?? '' }}</div>
                                        </div>
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <!-- Mobile Logout Button -->
                            <form method="POST" action="{{ route('logout') }}" class="sm:hidden px-3 pb-2">
                                @csrf
                                <button type="submit" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                    Logout
                                </button>
                            </form>
                        </header>

                        <!-- Main Content Area -->
                        <main class="flex-1 px-3 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8 overflow-y-auto">
                            @if (isset($header))
                                <div class="mb-6 rounded-xl sm:rounded-2xl border border-slate-200/80 bg-white/90 p-4 sm:p-6 shadow-sm">
                                    {{ $header }}
                                </div>
                            @endif

                            <div class="space-y-6">
                                @hasSection('content')
                                    @yield('content')
                                @else
                                    {{ $slot ?? '' }}
                                @endif
                            </div>
                        </main>
                    </div>
                </div>
            @else
                <main class="py-4 sm:py-8">
                    <div class="mx-auto w-full max-w-7xl px-3 sm:px-6 lg:px-8">
                        @hasSection('content')
                            @yield('content')
                        @else
                            {{ $slot ?? '' }}
                        @endif
                    </div>
                </main>
            @endunless
        </div>

        @stack('scripts')
    </body>
</html>
