<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        /* Evita el flash blanco en modo oscuro antes de cargar Tailwind */
        html[data-theme='dark'], html[data-theme='dark'] body { background-color: #0f172a !important; color-scheme: dark; }
        html[data-theme='light'], html[data-theme='light'] body { background-color: #ffffff !important; color-scheme: light; }
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.seo-meta', [
        'title' => $title ?? __('Login'),
        'description' => $description ?? config('app.name'),
        'image' => $image ?? asset('images/og-image.jpg'),
        'url' => url()->current()
    ])

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    {{-- Core Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Assets por sección (Vite Code Splitting) --}}
    @stack('page-assets')

    <style>
        .auth-gradient {
            background: radial-gradient(circle at top left, oklch(var(--p) / 0.15), transparent 40%),
                        radial-gradient(circle at bottom right, oklch(var(--s) / 0.15), transparent 40%);
        }
        .glass-card {
            background: oklch(var(--b1) / 0.8) !important;
            backdrop-filter: blur(12px);
            border: 1px solid oklch(var(--bc) / 0.1) !important;
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-base-200 auth-gradient flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center mb-6">
            <a href="{{ url('/') }}" class="text-3xl font-black text-primary tracking-tighter">
                {{ config('project.name') }}
            </a>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        @yield('content')

        {{-- Footer minimal con selectores --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 px-2 text-xs text-base-content/50">
            <div class="flex items-center gap-4">
                <x-language-selector class="dropdown-top" />
                <x-theme-toggle />
            </div>

            <div>
                &copy; {{ date('Y') }} {{ config('project.name') }}
            </div>
        </div>
    </div>

    {{--  TOAST area --}}
    <x-toast />
</body>
</html>
