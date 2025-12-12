<nav>
    <ul>
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('bible.index') }}">Bible</a></li>
        <li><a href="{{ route('program.index') }}">Program</a></li>
        <li><a href="{{ route('connect.index') }}">Connect</a></li>
        <li><a href="{{ route('news.index') }}">News</a></li>
        <li><a href="{{ route('resources.index') }}">Resources</a></li>
        <li><a href="{{ route('faq.index') }}">FAQ</a></li>
        <li><a href="{{ route('contact.index') }}">Contact</a></li>
        @can('admin')
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        @endcan
    </ul>

    <div class="nav-actions">
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
    </div>
</nav>
