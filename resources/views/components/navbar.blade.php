<nav>
    <ul>
        <li><a href="{{ url('/home') }}">Home</a></li>
        <li><a href="{{ url('/about') }}">About</a></li>
        <li><a href="{{ url('/sermons') }}">Sermons</a></li>
        <li><a href="{{ url('/articles') }}">Articles</a></li>
        <li><a href="{{ url('/resources') }}">Resources</a></li>
        <li><a href="{{ url('/contact') }}">Contact</a></li>
    </ul>

    @auth
        <span>{{ Auth::user()->name }}</span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Log out</button>
        </form>
    @else
        <a href="{{ route('login') }}">Login</a>
    @endauth
</nav>
