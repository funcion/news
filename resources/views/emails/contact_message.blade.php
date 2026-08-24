<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message - Glodaxia</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; line-height: 1.6; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background: #0f172a; padding: 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { margin: 4px 0 0 0; font-size: 12px; color: #94a3b8; }
        .content { padding: 28px 24px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; background: #ecfeff; color: #0891b2; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 16px; }
        .field-group { margin-bottom: 18px; }
        .label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 4px; }
        .value { font-size: 15px; color: #0f172a; font-weight: 600; }
        .message-box { background: #f1f5f9; border-radius: 8px; padding: 16px; border-left: 4px solid #06b6d4; font-size: 14px; color: #334155; white-space: pre-wrap; margin-top: 8px; }
        .meta { font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 16px; margin-top: 24px; display: flex; justify-content: space-between; }
        .footer { text-align: center; padding: 16px; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>GLODAXIA</h1>
            <p>Official Contact Form Notification</p>
        </div>
        <div class="content">
            <span class="badge">Inquiry: {{ $contactMessage->subject }}</span>
            
            <div class="field-group">
                <div class="label">Sender Name</div>
                <div class="value">{{ $contactMessage->name }}</div>
            </div>

            <div class="field-group">
                <div class="label">Sender Email</div>
                <div class="value"><a href="mailto:{{ $contactMessage->email }}" style="color: #0284c7; text-decoration: none;">{{ $contactMessage->email }}</a></div>
            </div>

            <div class="field-group">
                <div class="label">Subject</div>
                <div class="value">{{ $contactMessage->subject }}</div>
            </div>

            <div class="field-group">
                <div class="label">Message</div>
                <div class="message-box">{{ $contactMessage->message }}</div>
            </div>

            <div class="meta">
                <div>Locale: <strong>{{ strtoupper($contactMessage->locale) }}</strong> | IP: {{ $contactMessage->ip_address ?? 'N/A' }}</div>
                <div>Date: {{ $contactMessage->created_at->format('M d, Y H:i:s') }} UTC</div>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Glodaxia &bull; Automated Platform Communication &bull; <a href="mailto:hi@glodaxia.com" style="color: #94a3b8;">hi@glodaxia.com</a>
        </div>
    </div>
</body>
</html>