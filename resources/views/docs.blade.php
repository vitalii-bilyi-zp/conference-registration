<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Conference registration</title>

        <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@4.5.0/swagger-ui.css" />
        <script src="https://unpkg.com/swagger-ui-dist@4.5.0/swagger-ui-bundle.js" crossorigin></script>
    </head>
    <body>
        <div id="swagger-ui"></div>

        <script>
            window.onload = () => {
                window.ui = SwaggerUIBundle({
                    url: "{{ asset('openapi.json') }}",
                    dom_id: '#swagger-ui',
                });
            };
        </script>
    </body>
</html>
