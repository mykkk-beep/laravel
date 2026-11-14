<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('vendor.laravelpwa.meta')

        <title>{{ config('app.name', 'QR Attendance') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .guest-background {
                position: relative;
                isolation: isolate;
                overflow: hidden;
            }

            .guest-background::before,
            .guest-background::after {
                content: "";
                position: absolute;
                inset: 0;
                z-index: -1;
            }

            .guest-background::before {
                inset: -8px;
                background: url("{{ asset('images/classroom-background.jpg') }}") center / cover no-repeat;
                filter: blur(6px);
            }

            .guest-background::after {
                background: rgba(255, 255, 255, 0.35);
            }

            .guest-card {
                width: min(100%, 28rem);
                padding: clamp(24px, 6vw, 40px);
                background:
                    linear-gradient(rgba(255, 255, 255, 0.82), rgba(255, 255, 255, 0.82)),
                    url("{{ asset('images/classroom-background.jpg') }}") center / cover no-repeat;
                border-radius: clamp(20px, 5vw, 28px);
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="guest-background flex min-h-screen flex-col items-center justify-center px-4 py-6 sm:px-6 sm:py-10">
            <div class="guest-card relative z-10">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </div>
    </body>
</html>
