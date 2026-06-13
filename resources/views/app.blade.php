<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Smart Waste System') }}</title>

        @fonts
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="overflow-x-hidden font-sans antialiased text-gray-900 bg-gray-50 max-w-[100vw]">
        @inertia
    </body>
</html>