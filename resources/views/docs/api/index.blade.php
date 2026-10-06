<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Swagger API Integrasi - ETAMKERJA</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui.css">
    <style>
        body { margin: 0; background: #fafafa; }
        .topbar-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            background: #1b4332;
            color: #e9f5ee;
            font-family: system-ui, -apple-system, Segoe UI, sans-serif;
        }
        .topbar-custom h1 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }
        .topbar-custom .meta {
            font-size: .85rem;
            opacity: .85;
        }
        .topbar-custom form { margin: 0; }
        .topbar-custom button {
            border: 0;
            border-radius: 8px;
            padding: 8px 12px;
            background: #52b788;
            color: #081c15;
            font-weight: 700;
            cursor: pointer;
        }
        .swagger-ui .topbar { display: none; }
        #swagger-ui { max-width: 1460px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="topbar-custom">
        <div>
            <h1>ETAMKERJA — Swagger API Integrasi</h1>
            <div class="meta">Login sebagai: {{ $username }} · Spec: OpenAPI 3.0</div>
        </div>
        <form method="POST" action="{{ route('docs.api.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-bundle.js" crossorigin></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-standalone-preset.js" crossorigin></script>
    <script>
        window.ui = SwaggerUIBundle({
            url: @json($openapiUrl),
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: 'StandaloneLayout',
            persistAuthorization: true,
            tryItOutEnabled: true,
            docExpansion: 'list',
            defaultModelsExpandDepth: 1,
            validatorUrl: null
        });
    </script>
</body>
</html>
