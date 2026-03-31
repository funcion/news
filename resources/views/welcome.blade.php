<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Noticias Platform</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-950 text-white min-h-screen flex flex-col font-sans">
    <div class="container max-w-6xl mx-auto px-4 py-8 flex-1">
        <header class="text-center py-8">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-amber-500 to-red-500 bg-clip-text text-transparent">
                🗞️ Noticias Platform
            </h1>
            <p class="text-lg text-gray-300 mb-8">Plataforma de noticias automatizada con IA</p>
        </header>

        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 md:p-8 mb-8 border border-white/20">
            <h2 class="text-2xl font-bold text-emerald-400 mb-4">✅ Sistema Operativo</h2>
            <p class="text-gray-200 mb-6">Laravel {{ app()->version() }} está funcionando correctamente con FrankenPHP</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white/5 rounded-lg p-4 text-center border border-white/10">
                    <h3 class="text-lg font-semibold text-blue-400 mb-2">🐘 PHP</h3>
                    <p class="text-gray-300 text-sm">{{ phpversion() }}</p>
                </div>
                <div class="bg-white/5 rounded-lg p-4 text-center border border-white/10">
                    <h3 class="text-lg font-semibold text-blue-400 mb-2">🗄️ PostgreSQL</h3>
                    <p class="text-gray-300 text-sm">Con pgvector</p>
                </div>
                <div class="bg-white/5 rounded-lg p-4 text-center border border-white/10">
                    <h3 class="text-lg font-semibold text-blue-400 mb-2">🔴 Redis</h3>
                    <p class="text-gray-300 text-sm">Cache & Queues</p>
                </div>
                <div class="bg-white/5 rounded-lg p-4 text-center border border-white/10">
                    <h3 class="text-lg font-semibold text-blue-400 mb-2">⚡ FrankenPHP</h3>
                    <p class="text-gray-300 text-sm">HTTP/3 Ready</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
            <a href="/admin" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-300 text-center shadow-lg shadow-blue-500/20">
                🔐 Panel de Administración
            </a>
            <a href="/up" class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-300 text-center shadow-lg shadow-emerald-500/20">
                ❤️ Health Check
            </a>
        </div>
    </div>

    <footer class="text-center py-8 text-gray-400 text-sm border-t border-white/10">
        <p>Noticias Platform © {{ date('Y') }} - Desarrollado con Laravel 12, Filament v3 y FrankenPHP</p>
    </footer>
</body>
</html>
