<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @unless(View::hasSection('hide_navbar'))
        <header>@include('components.navbar')</header>
    @endunless

    <main>
        @unless(View::hasSection('hide_title'))
            <h1>@yield('title')</h1>
        @endunless

        @can('admin')
            @yield('admin-header')
        @endcan

        @yield('content')

        {{ $slot ?? null }}
    </main>

    @unless(View::hasSection('hide_footer'))
        @include('components.footer')
    @endunless
</body>
</html>
