<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Smart Attendance</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
        }

        .container {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
        }

        .container::before,
        .container::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
        }

        .container::before {
            inset: -8px;
            background: url("{{ asset('images/classroom-background.jpg') }}") center / cover no-repeat;
            filter: blur(6px);
        }

        .container::after {
            background: rgba(255, 255, 255, 0.35);
        }

        .card {
            width: min(750px, 100%);
            background:
                linear-gradient(rgba(255, 255, 255, 0.82), rgba(255, 255, 255, 0.82)),
                url("{{ asset('images/classroom-background.jpg') }}") center / cover no-repeat;
            padding: clamp(40px, 10vw, 120px);
            border-radius: 36px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .logo {
            width: 55px;
            height: 55px;
            margin: 0 auto 15px;
            background: #16a34a;
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            font-weight: bold;
        }

        h1 {
            font-size: 50px;
            color: #111827;
            margin-bottom: 8px;
        }

        p {
            color: #6b7280;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        a {
            text-decoration: none;
            padding: 11px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .login {
            background: #16a34a;
            color: white;
        }

        .login:hover {
            background: #15803d;
        }

        .register {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(to right, #020617, #4338ca);
            color: white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            transition: background 150ms ease, box-shadow 150ms ease;
        }

        .register:hover {
            background: linear-gradient(to right, #1e293b, #4f46e5);
        }

        .register:focus-visible {
            outline: 2px solid #6366f1;
            outline-offset: 2px;
        }

        .dashboard {
            background: #16a34a;
            color: white;
        }

        .dashboard:hover {
            background: #15803d;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 36px;
            }

            p {
                font-size: 20px;
            }

            .buttons {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="card">

            <div class="logo">
                <svg width="38" height="38" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path fill="white" d="M4 4h14v14H4zM30 4h14v14H30zM4 30h14v14H4z"/>
                    <path fill="#16a34a" d="M7 7h8v8H7zM33 7h8v8h-8zM7 33h8v8H7z"/>
                    <path fill="white" d="M9 9h4v4H9zM35 9h4v4h-4zM9 35h4v4H9z"/>
                    <path fill="white" d="M21 4h5v5h-5zM21 12h5v5h-5zM30 21h5v5h-5zM38 21h6v5h-6zM21 30h5v5h-5zM30 38h5v6h-5zM38 30h6v5h-6zM21 39h5v5h-5z"/>
                    <circle cx="25" cy="25" r="8" fill="#15803d" stroke="white" stroke-width="2"/>
                    <path d="m21.5 25 2.3 2.3 4.7-5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h1>Smart Attendance</h1>

            <p>QR-based attendance management system</p>

            <div class="buttons">

                @if (Route::has('login'))

                    @auth

                        <a href="{{ url('/dashboard') }}" class="dashboard">
                            Dashboard
                        </a>

                    @else

                        <a href="{{ route('login') }}" class="login">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="register">
                                Register
                            </a>
                        @endif

                    @endauth

                @endif

            </div>

        </div>

    </div>

</body>

</html>
