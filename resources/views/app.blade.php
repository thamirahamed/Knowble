<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" type="image/png" href="/images/webmanifest/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/images/webmanifest/favicon.svg" />
        <link rel="shortcut icon" href="/images/webmanifest/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/images/webmanifest/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="Knowble" />
        <link rel="manifest" href="/images/webmanifest/manifest.json" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <script src="https://meet.jit.si/external_api.js"></script>


        <!-- Fonts -->
        <link href="https://api.fontshare.com/v2/css?f[]=satoshi@1&f[]=clash-display@1&display=swap" rel="stylesheet">
        <link href="https://api.fontshare.com/v2/css?f[]=expose@1&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans scroll-smooth">
        @inertia
    </body>
</html>
