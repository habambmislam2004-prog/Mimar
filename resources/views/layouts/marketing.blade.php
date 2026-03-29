<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? "Mi'mar" }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap');

        * {
            box-sizing: border-box;
        }

        html, body {
            min-height: 100%;
        }

        body {
            margin: 0;
            font-family: "IBM Plex Sans Arabic", "Inter", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59,76,202,0.08), transparent 20%),
                radial-gradient(circle at bottom right, rgba(15,23,42,0.05), transparent 24%),
                #f3f6fb;
            color: #0f172a;
        }

        html[dir="ltr"] body {
            font-family: "Inter", "IBM Plex Sans Arabic", sans-serif;
        }

        a, button, input, select, textarea {
            font-family: inherit;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>