<nav x-data="{ open: false }" class="bg-white/95 border-b border-slate-200/80 backdrop-blur-sm shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                    <x-application-logo class="block h-10 w-10 fill-current text-slate-900" />
                    <span class="text-lg font-semibold text-slate-900">{{ config('app.name', 'QR Attendance') }}</span>
                </a>

                <div class="hidden sm:flex sm:items-center sm:space-x-4">
                    @php $user = Auth::user(); $isSuperAdmin = $user?->role === \App\Models\User::ROLE_SUPERADMIN; @endphp

                    @if($isSuperAdmin)
                        <x-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('superadmin.teachers.index')" :active="request()->routeIs('superadmin.teachers.*')">
                            {{ __('Teachers') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('teacher.classes.index')" :active="request()->routeIs('teacher.classes.*')">
                            {{ __('Classes') }}
                        </x-nav-link>

                        <x-nav-link :href="route('teacher.students.all')" :active="request()->routeIs('teacher.students.*')">
                            {{ __('Students') }}
                        </x-nav-link>

                        <x-nav-link :href="route('teacher.attendance.records')" :active="request()->routeIs('teacher.attendance.*')">
                            {{ __('Records') }}
                        </x-nav-link>

                        <x-nav-link :href="route('teacher.recommendations')" :active="request()->routeIs('teacher.recommendations')">
                            {{ __('Recommendation') }}
                        </x-nav-link>

                        <x-nav-link :href="route('teacher.notifications')" :active="request()->routeIs('teacher.notifications')">
                            {{ __('Notifications') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:space-x-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                            <span>{{ $user?->name ?? 'Guest' }}</span>
                            <svg class="h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="rounded-3xl bg-white shadow-card ring-1 ring-slate-200/80 p-2">
                            @if($user)
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('View Profile') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Edit Profile') }}
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            @endif
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 bg-white/95 px-4 pt-4 pb-3 border-b border-slate-200/80">
            @php $user = Auth::user(); $isSuperAdmin = $user?->role === \App\Models\User::ROLE_SUPERADMIN; @endphp

            @if($isSuperAdmin)
                <x-responsive-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('superadmin.teachers.index')" :active="request()->routeIs('superadmin.teachers.*')">
                    {{ __('Teachers') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('superadmin.profile')" :active="request()->routeIs('superadmin.profile')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('teacher.classes.index')" :active="request()->routeIs('teacher.classes.*')">
                    {{ __('Classes') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('teacher.students.all')" :active="request()->routeIs('teacher.students.*')">
                    {{ __('Students') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('teacher.attendance.records')" :active="request()->routeIs('teacher.attendance.*')">
                    {{ __('Records') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('teacher.recommendations')" :active="request()->routeIs('teacher.recommendations')">
                    {{ __('Recommendation') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('teacher.notifications')" :active="request()->routeIs('teacher.notifications')">
                    {{ __('Notifications') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="space-y-1 bg-white/95 px-4 py-3">
            <div class="font-medium text-base text-slate-900">{{ $user?->name ?? 'Guest' }}</div>
            <div class="font-medium text-sm text-slate-500">{{ $user?->email ?? '' }}</div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>