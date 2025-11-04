<nav>
    <ul>
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('bible') }}">Bible</a></li>
        <li><a href="{{ route('sermons') }}">Sermons</a></li>
        <li><a href="{{ route('resources') }}">Resources</a></li>
        <li><a href="{{ route('contact') }}">Contact</a></li>
    </ul>

    @auth
        {{--User profile--}}
        <a href="{{ route('profile.edit') }}">
            <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/default-avatar.svg') }}" alt="Avatar" class="avatar">
            <span>{{ Auth::user()->name }}</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Log out</button>
        </form>
    @else
        {{--Guest profile--}}
        <a href="{{ route('login') }}">
            <img src="{{ asset('images/default-avatar.svg') }}" alt="Avatar" class="avatar">
            <span>Guest</span>
        </a>
    @endauth
</nav>
