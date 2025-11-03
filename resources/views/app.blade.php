<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- <title inertia>{{ config('app.name', 'Laravel') }}</title> -->
        <title>MARKETMIS</title>

        <!-- Fonts -->
        <link rel="icon" href="{{ asset('images/marketIS-logo.ico') }}" type="image/x-icon">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
        <link
            rel="stylesheet"
            href="//cdn.jsdelivr.net/npm/element-plus/dist/index.css"
        />
        <!-- Import Vue 3 -->
        <script src="//cdn.jsdelivr.net/npm/vue@3"></script>
        <!-- Import component library -->
        <script src="//cdn.jsdelivr.net/npm/element-plus"></script>
        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
