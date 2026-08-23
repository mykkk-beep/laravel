<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QR Attendance') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-0 bg-slate-50">
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white/95 shadow-card rounded-[32px] border border-slate-200/80">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </div>
    </body>
</html>
