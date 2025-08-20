<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Contact Manager</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 font-sans text-gray-800">
        <div class="container mx-auto p-4">
            <header class="mb-8">
                <h1 class="text-3xl font-bold">
                    <a href="{{ route('contacts.index') }}">Contact Manager</a>
                </h1>
            </header>

            <main>
                @yield('content')
            </main>
        </div>
    </body>
</html>
