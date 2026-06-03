<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07); }
        .header { padding: 24px 32px; border-bottom: 1px solid #e5e7eb; }
        .header h1 { margin: 0; font-size: 18px; color: #111827; }
        .body { padding: 24px 32px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 13px; font-weight: 600; }
        .status-published { background: #d1fae5; color: #065f46; }
        .status-pending_review { background: #fef3c7; color: #92400e; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-draft { background: #e5e7eb; color: #374151; }
        .info-row { margin: 12px 0; }
        .info-label { font-size: 12px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em; }
        .info-value { font-size: 15px; color: #111827; margin-top: 2px; }
        .btn { display: inline-block; padding: 10px 24px; background: #f59e0b; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; margin-top: 16px; }
        .footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📰 Cambio de Estado de Artículo</h1>
        </div>
        <div class="body">
            <div class="info-row">
                <div class="info-label">Artículo</div>
                <div class="info-value">{{ $article->getTranslation('title', 'en') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Cambio de Estado</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $oldStatus }}">{{ $oldStatus }}</span>
                    →
                    <span class="status-badge status-{{ $newStatus }}">{{ $newStatus }}</span>
                </div>
            </div>
            @if($changedBy)
            <div class="info-row">
                <div class="info-label">Cambiado por</div>
                <div class="info-value">{{ $changedBy }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Categoría</div>
                <div class="info-value">{{ $article->category?->getTranslation('name', 'en') ?? 'Sin categoría' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha</div>
                <div class="info-value">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <a href="{{ url('/admin/articles/' . $article->id . '/edit') }}" class="btn">Ver Artículo en Admin</a>
        </div>
        <div class="footer">
            Glodaxia — Notificación automática del sistema editorial
        </div>
    </div>
</body>
</html>
