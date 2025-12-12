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

    {{--Layout for home page and admin dashboard, each implementing some sections--}}
    <main>
        <h1>@yield('title')</h1>

        @hasSection('users')
            <section class="dashboard-section section-users">
                @yield('users')
            </section>
        @endif

        @hasSection('bible')
            <section class="dashboard-section section-bible">
                @yield('bible')
            </section>
        @endif

        @hasSection('news')
            <section class="dashboard-section section-news">
                @yield('news')
            </section>
        @endif

        @hasSection('program')
            <section class="dashboard-section section-program">
                @yield('program')
            </section>
        @endif

        @hasSection('connect')
            <section class="dashboard-section section-connect">
                @yield('connect')
            </section>
        @endif

        @hasSection('resources')
            <section class="dashboard-section section-resources">
                @yield('resources')
            </section>
        @endif

        @hasSection('faq')
            <section class="dashboard-section section-faq">
                @yield('faq')
            </section>
        @endif

        @hasSection('contact')
            <section class="dashboard-section section-contact">
                @yield('contact')
            </section>
        @endif

        {{ $slot ?? null }}
    </main>

    @include('components.footer')
</body>
</html>
