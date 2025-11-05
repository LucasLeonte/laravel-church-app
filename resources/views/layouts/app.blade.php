<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header>@include('components.navbar')</header>

    <main>
        <h1>@yield('title')</h1>

        @if(\App\Helpers\isAdmin())
            @yield('admin')
        @endif

        @yield('content')

        {{ $slot ?? null }}
    </main>

    @include('components.footer')
</body>
</html>
