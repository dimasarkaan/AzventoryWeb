<!doctype html>
<html>
<head>
    <title>{!! $metadata['title'] !!}</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" href="{{ asset('logo.svg') }}?v=2" type="image/svg+xml">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom Logo for Scalar */
        .scalar-logo {
            content: url('{{ asset("logo.svg") }}');
            width: 24px;
            height: 24px;
        }
        
        /* Tema Terang (Light Mode) */
        :root {
            --scalar-color-accent: #2563eb; /* Azventory Primary 600 */
            --scalar-button-1: #2563eb;
            --scalar-button-1-hover: #1d4ed8;
            --scalar-button-1-color: #ffffff;
            --scalar-background-2: #f8fafc; /* Azventory Background */
        }
        
        /* Sembunyikan elemen bawaan yang tidak diperlukan (UX Improvement) */
        .dark-mode {
            --scalar-color-accent: #3b82f6; /* Azventory Primary 500 */
            --scalar-button-1: #3b82f6;
            --scalar-button-1-hover: #60a5fa;
            --scalar-button-1-color: #ffffff;
        }
        
        /* Sembunyikan tulisan Download & Watermark */
        a[href*="scalar.com"], 
        .scalar-client-powered-by {
            display: none !important;
        }

        /* Sembunyikan fitur Ask AI (Gagal Fetch karena butuh OpenAI key) */
        button[aria-label="Ask AI Agent"],
        .scalar-app [aria-label*="Ask AI"],
        .ask-ai-button {
            display: none !important;
        }
    </style>
</head>
<body>

<script id="api-reference" type="application/json"
    data-configuration='{"proxy": "", "hideDownloadButton": false, "hideModels": true, "agent": {"disabled": true}, "hiddenClients": {"ruby": true, "python": true, "java": true, "c": true, "csharp": true, "swift": true, "kotlin": true, "objectivec": true, "go": true, "clojure": true, "ocaml": true, "r": true, "node": true}}'
@foreach($htmlAttributes as $attribute => $value)
    {!! $attribute !!}="{!! $value !!}"
@endforeach
>
{!! file_get_contents(storage_path('app/private/scribe/openapi.yaml')) !!}
</script>
<script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
</body>
</html>
