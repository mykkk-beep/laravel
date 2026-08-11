<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
                    <aside class="w-full lg:w-72 xl:w-80 border-b lg:border-b-0 lg:border-r border-slate-200/80 bg-slate-950 text-slate-100">
                        <div class="flex items-center gap-3 border-b border-white/10 px-6 py-6">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8 10h8" stroke-linecap="round" />
                                    <path d="M8 14h5" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold">{{ config('app.name', 'QR Attendance') }}</div>
                                <div class="text-xs text-slate-400">Admin workspace</div>
                            </div>
                        </div>

                        <nav class="space-y-1 px-4 py-5">
                            @if($isSuperAdmin)
                                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◉</span>
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ route('superadmin.teachers.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('superadmin.teachers.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◌</span>
                                    <span>Teachers</span>
                                </a>
                                <a href="{{ route('superadmin.profile') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('superadmin.profile') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◌</span>
                                    <span>Profile</span>
                                </a>
                            @elseif($isTeacher)
                                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('teacher.dashboard') ? 'bg-white/10 text-black' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◉</span>
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ route('teacher.classes.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('teacher.classes.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◌</span>
                                    <span>Classes</span>
                                </a>
                                <a href="{{ route('teacher.students.all') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('teacher.students.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◌</span>
                                    <span>Students</span>
                                </a>
                                <a href="{{ route('teacher.subjects.index') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('teacher.subjects.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◌</span>
                                    <span>Subjects</span>
                                </a>
                                <a href="{{ route('teacher.attendance.records') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('teacher.attendance.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◌</span>
                                    <span>Attendance</span>
                                </a>
                                <a href="{{ route('teacher.recommendations') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('teacher.recommendations') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◌</span>
                                    <span>Recommendations</span>
                                </a>
                                <a href="{{ route('teacher.notifications') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs('teacher.notifications') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <span>◌</span>
                                    <span>Notifications</span>
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition text-slate-300 hover:bg-white/10 hover:text-white">
                                    <span>◉</span>
                                    <span>Dashboard</span>
                                </a>
                            @endif
                        </nav>
                    </aside>

                    <div class="flex flex-1 flex-col">
                        <header class="border-b border-slate-200/80 bg-white/90 backdrop-blur">
                            <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">@yield('pageTitle', 'Dashboard')</p>
                                    <p class="text-xs text-slate-500">@yield('pageSubtitle', 'Welcome back')</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <label class="hidden items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500 md:flex">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m21 21-4.35-4.35" stroke-linecap="round" /><circle cx="11" cy="11" r="5" /></svg>
                                        <input type="text" placeholder="Search" class="w-28 bg-transparent outline-none placeholder:text-slate-400" />
                                    </label>

                                    <a href="{{ route('teacher.notifications') }}" class="rounded-2xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm">🔔</a>

                                    <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="hidden sm:block">
                                            <div class="text-sm font-semibold text-slate-900">{{ $user?->name ?? 'Guest' }}</div>
                                            <div class="text-xs text-slate-500">{{ $user?->email ?? '' }}</div>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </header>

                        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                            @if (isset($header))
                                <div class="mb-6 rounded-3xl border border-slate-200/80 bg-white/90 p-6 shadow-sm">
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
                <main class="py-8">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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
