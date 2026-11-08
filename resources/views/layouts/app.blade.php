<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('vendor.laravelpwa.meta')

        <title>{{ config('app.name', 'QR Attendance') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen bg-slate-50">
            @unless(View::hasSection('showNavigation') && trim(View::getSection('showNavigation')) === 'false')
                @php
                    $user = Auth::user();
                    $isSuperAdmin = $user?->role === \App\Models\User::ROLE_SUPERADMIN;
                    $isTeacher = $user?->role === \App\Models\User::ROLE_TEACHER;
                @endphp

                <div class="flex min-h-screen flex-col lg:h-screen lg:flex-row lg:overflow-hidden">
                    <!-- Sidebar - Mobile Responsive -->
                    <aside class="flex w-full flex-col border-b border-slate-200/80 bg-slate-950 text-slate-100 lg:sticky lg:top-0 lg:h-screen lg:w-72 lg:border-b-0 lg:border-r xl:w-80">
                        <!-- Logo Section -->
                        <div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-4 sm:px-6 sm:py-5">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-xl sm:rounded-2xl bg-green-600 shrink-0">
                                    <svg width="32" height="32" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path fill="white" d="M4 4h14v14H4zM30 4h14v14H30zM4 30h14v14H4z"/>
                                        <path fill="#16a34a" d="M7 7h8v8H7zM33 7h8v8h-8zM7 33h8v8H7z"/>
                                        <path fill="white" d="M9 9h4v4H9zM35 9h4v4h-4zM9 35h4v4H9z"/>
                                        <path fill="white" d="M21 4h5v5h-5zM21 12h5v5h-5zM30 21h5v5h-5zM38 21h6v5h-6zM21 30h5v5h-5zM30 38h5v6h-5zM38 30h6v5h-6zM21 39h5v5h-5z"/>
                                        <circle cx="25" cy="25" r="8" fill="#15803d" stroke="white" stroke-width="2"/>
                                        <path d="m21.5 25 2.3 2.3 4.7-5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm font-semibold truncate">{{ config('app.name', 'QR Attendance') }}</div>
                                    <div class="text-xs text-slate-400 truncate">Admin workspace</div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Menu -->
                        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 sm:px-4 sm:py-5 lg:min-h-0">
                            @if($isSuperAdmin)
                                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10Zm0 8h8v-4H3v4Zm12 0h6V11h-6v10Zm0-18v4h6V3h-6Z" />
                                    </svg>
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ route('superadmin.teachers.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('superadmin.teachers.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75M14 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                                    </svg>
                                    <span>Teachers</span>
                                </a>

                            @elseif($isTeacher)
                                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.dashboard') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10Zm0 8h8v-4H3v4Zm12 0h6V11h-6v10Zm0-18v4h6V3h-6Z" />
                                    </svg>
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ route('teacher.classes.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.classes.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z" />
                                    </svg>
                                    <span>Classes</span>
                                </a>
                                <a href="{{ route('teacher.students.all') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.students.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75M14 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                                    </svg>
                                    <span>Students</span>
                                </a>
                                <a href="{{ route('teacher.attendance.records') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.attendance.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8m-9 4h10M7 3h10a2 2 0 0 1 2 2v16H5V5a2 2 0 0 1 2-2Zm2 9h6m-6 4h6" />
                                    </svg>
                                    <span>Records</span>
                                </a>
                                <a href="{{ route('teacher.recommendations') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition {{ request()->routeIs('teacher.recommendations') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18h6m-5 4h4m-2-20a7 7 0 0 0-4 12.75c.6.4 1 1.05 1 1.75h6c0-.7.4-1.35 1-1.75A7 7 0 0 0 12 2Z" />
                                    </svg>
                                    <span>Recommendations</span>
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition text-slate-300 hover:bg-white/10 hover:text-white">
                                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10Zm0 8h8v-4H3v4Zm12 0h6V11h-6v10Zm0-18v4h6V3h-6Z" />
                                    </svg>
                                    <span>Dashboard</span>
                                </a>
                            @endif
                        </nav>

                        <form method="POST" action="{{ route('logout') }}" class="mt-auto border-t border-white/10 p-3 sm:p-4">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 rounded-xl border border-white/10 px-3 py-3 text-left text-sm font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5m5 5H3m9-9h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" />
                                </svg>
                                <span>Log out</span>
                            </button>
                        </form>
                    </aside>

                    <div class="flex flex-1 flex-col lg:min-h-0">
                        <!-- Header - Mobile Optimized -->
                        <header class="sticky top-0 z-40 shrink-0 border-b border-slate-200/80 bg-white/90 backdrop-blur">
                            <div class="flex flex-col gap-3 px-3 py-3 sm:px-6 sm:py-4 lg:px-8 lg:flex-row lg:items-center lg:justify-between">
                                <div class="min-w-0">
                                    <p class="@yield('pageTitleClass', 'text-xs sm:text-sm') font-semibold text-slate-900">@yield('pageTitle', 'Dashboard')</p>
                                    <p class="@yield('pageSubtitleClass', 'text-xs') text-slate-500">@yield('pageSubtitle', 'Welcome back')</p>
                                </div>

                                <div class="flex items-center gap-2 sm:gap-3">
                                    @if($isSuperAdmin)
                                        <div x-data="{ open: false }" class="relative">
                                            <button type="button" @click="open = !open" @keydown.escape.window="open = false" :aria-expanded="open.toString()" aria-controls="pending-teacher-notifications" aria-label="Pending teacher registrations{{ ($pendingTeacherCount ?? 0) > 0 ? ', '.$pendingTeacherCount.' awaiting approval' : '' }}" title="Pending teacher registrations" class="relative rounded-lg border border-slate-200 bg-white p-2 text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 12a2 2 0 0 0 4 0" />
                                                </svg>
                                                @if(($pendingTeacherCount ?? 0) > 0)
                                                    <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">{{ $pendingTeacherCount }}</span>
                                                @endif
                                            </button>

                                            <div id="pending-teacher-notifications" x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false" class="absolute right-0 z-50 mt-3 w-[min(24rem,calc(100vw-2rem))] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                                                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                                                    <div>
                                                        <h2 class="font-semibold text-slate-900">Account registrations</h2>
                                                        <p class="text-xs text-slate-500">{{ $pendingTeacherCount ?? 0 }} awaiting approval</p>
                                                    </div>
                                                    <button type="button" @click="open = false" class="rounded-md px-2 py-1 text-sm text-slate-500 hover:bg-slate-100" aria-label="Close notifications">Close</button>
                                                </div>
                                                <div class="max-h-[65vh] overflow-y-auto">
                                                    @forelse($pendingTeachers ?? collect() as $pendingTeacher)
                                                        <div class="border-b border-slate-100 px-4 py-3 last:border-b-0">
                                                            <div class="font-medium text-slate-900">{{ $pendingTeacher->name }}</div>
                                                            <div class="mt-0.5 break-all text-xs text-slate-500">{{ $pendingTeacher->email }}</div>
                                                            <div class="mt-1 text-xs text-slate-400">Registered {{ $pendingTeacher->created_at?->diffForHumans() }}</div>
                                                            <div class="mt-3 flex gap-2">
                                                                <form method="POST" action="{{ route('superadmin.teachers.approve', $pendingTeacher) }}">
                                                                    @csrf
                                                                    <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700">Accept</button>
                                                                </form>
                                                                <form method="POST" action="{{ route('superadmin.teachers.decline', $pendingTeacher) }}" onsubmit="return confirm('Refuse this account registration?');">
                                                                    @csrf
                                                                    <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Refuse</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="px-4 py-6 text-center text-sm text-slate-500">There are no pending registrations.</p>
                                                    @endforelse
                                                </div>
                                                <a href="{{ route('superadmin.teachers.index') }}" class="block border-t border-slate-100 bg-slate-50 px-4 py-3 text-center text-sm font-semibold text-indigo-600 hover:bg-slate-100">Manage teacher accounts</a>
                                            </div>
                                        </div>
                                    @elseif($isTeacher)
                                        <a href="{{ route('teacher.notifications') }}" class="relative rounded-lg border border-slate-200 bg-white p-2 text-slate-600 shadow-sm hover:border-slate-300 hover:bg-slate-50 transition text-base" aria-label="Notifications{{ ($pendingRecommendationReplyCount ?? 0) > 0 ? ', '.$pendingRecommendationReplyCount.' recommendation replies awaiting your response' : '' }}" title="Notifications">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 12a2 2 0 0 0 4 0" />
                                            </svg>
                                            @if(($pendingRecommendationReplyCount ?? 0) > 0)
                                                <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">{{ $pendingRecommendationReplyCount }}</span>
                                            @endif
                                        </a>
                                    @endif

                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 sm:gap-3 rounded-lg border border-slate-200 bg-slate-50 px-2 sm:px-3 py-2 transition hover:border-slate-300 hover:bg-slate-100" aria-label="Edit profile" title="Edit profile">
                                        <div class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-slate-900 text-xs sm:text-sm font-semibold text-white shrink-0">
                                            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="hidden sm:block min-w-0">
                                            <div class="text-xs sm:text-sm font-semibold text-slate-900 truncate">{{ $user?->name ?? 'Guest' }}</div>
                                            <div class="text-xs text-slate-500 truncate">{{ $user?->email ?? '' }}</div>
                                        </div>
                                    </a>

                                </div>
                            </div>
                        </header>

                        <!-- Main Content Area -->
                        <main class="flex-1 px-3 py-4 sm:px-6 sm:py-6 lg:min-h-0 lg:overflow-y-auto lg:px-8 lg:py-8">
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
