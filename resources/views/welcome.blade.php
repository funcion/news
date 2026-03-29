<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Noticias Platform</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: #fff; min-height: 100vh; display: flex; flex-direction: column; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; flex: 1; }
        header { padding: 2rem 0; text-align: center; }
        h1 { font-size: 3rem; margin-bottom: 1rem; background: linear-gradient(90deg, #f39c12, #e74c3c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .subtitle { font-size: 1.2rem; color: #a0a0a0; margin-bottom: 2rem; }
        .status { background: rgba(255,255,255,0.1); border-radius: 10px; padding: 2rem; margin: 2rem 0; }
        .status h2 { color: #2ecc71; margin-bottom: 1rem; }
        .services { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .service { background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; text-align: center; }
        .service h3 { color: #3498db; margin-bottom: 0.5rem; }
        .service p { color: #a0a0a0; font-size: 0.9rem; }
        .links { display: flex; justify-content: center; gap: 1rem; margin-top: 2rem; }
        .link { background: #3498db; color: white; padding: 0.8rem 1.5rem; border-radius: 5px; text-decoration: none; transition: background 0.3s; }
        .link:hover { background: #2980b9; }
        footer { text-align: center; padding: 2rem; color: #a0a0a0; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🗞️ Noticias Platform</h1>
            <p class="subtitle">Plataforma de noticias automatizada con IA</p>
        </header>

        <div class="status">
            <h2>✅ Sistema Operativo</h2>
            <p>Laravel {{ app()->version() }} está funcionando correctamente con FrankenPHP</p>
            
            <div class="services">
                <div class="service">
                    <h3>🐘 PHP</h3>
                    <p>{{ phpversion() }}</p>
                </div>
                <div class="service">
                    <h3>🗄️ PostgreSQL</h3>
                    <p>Con pgvector</p>
                </div>
                <div class="service">
                    <h3>🔴 Redis</h3>
                    <p>Cache & Queues</p>
                </div>
                <div class="service">
                    <h3>⚡ FrankenPHP</h3>
                    <p>HTTP/3 Ready</p>
                </div>
            </div>
        </div>

        <div class="links">
            <a href="/admin" class="link">🔐 Panel de Administración</a>
            <a href="/up" class="link">❤️ Health Check</a>
        </div>
    </div>

    <footer>
        <p>Noticias Platform © {{ date('Y') }} - Desarrollado con Laravel 12, Filament v3 y FrankenPHP</p>
    </footer>
</body>
</html>
