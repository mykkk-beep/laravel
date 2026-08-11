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
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 400px;
            background: white;
            padding: 40px;
            border-radius: 12px;
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
            font-size: 25px;
            color: #111827;
            margin-bottom: 8px;
        }

        p {
            color: #6b7280;
            font-size: 14px;
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
            background: #f3f4f6;
            color: #374151;
        }

        .register:hover {
            background: #e5e7eb;
        }

        .dashboard {
            background: #16a34a;
            color: white;
        }

        .dashboard:hover {
            background: #15803d;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="card">

            <div class="logo">
                ✓
            </div>

            <h1>SmartAttendance</h1>

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
