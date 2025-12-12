@extends('layouts.dashboard')

@section('title', 'Home')

@section('bible')
    <div class="home-bible-verse">
        @if(isset($dailyVerse))
            <h2>Daily Verse</h2>
            <blockquote>
                <p>{{ $dailyVerse['text'] }}</p>
                <footer>{{ $dailyVerse['reference'] }}</footer>
            </blockquote>
        @else
            <p>No verse for today.</p>
        @endif
    </div>

    <p><a href="{{ route('bible.index') }}">Read Bible</a></p>
@endsection

@section('news')
    <h2>Latest News</h2>
    @if(isset($news) && $news->count())
        <ul>
            @foreach($news as $n)
                <li>
                    @if(!empty($n->image))
                        <p>
                            <img src="{{ Str::startsWith($n->image, 'images/') ? asset('storage/' . $n->image) : asset($n->image) }}" alt="{{ $n->title }}">
                        </p>
                    @endif

                    <strong>{{ $n->title }}</strong>
                    <div>{{ Str::limit($n->content, 150) }}</div>

                </li>
            @endforeach
        </ul>
    @else
        <p>No news available.</p>
    @endif
    <p><a href="{{ route('news.index') }}">See All News</a></p>
@endsection

@section('program')
    <h2>Upcoming Services</h2>
    @if(isset($programs) && $programs->count())
        <ul>
            @foreach($programs as $p)
                <li>
                    <strong>{{ $p->title }}</strong>
                    <div>{{ $p->description }}</div>
                    <div>
                        @if(isset($p->day_offset) && $p->day_offset === 0)
                            Today
                        @elseif(isset($p->day_offset) && $p->day_offset === 1)
                            Tomorrow
                        @else
                            In {{ $p->day_offset }} days
                        @endif
                        &nbsp;|&nbsp; {{ $p->start_time }} - {{ $p->end_time }}
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p>No upcoming programs.</p>
    @endif
    <p><a href="{{ route('program.index') }}">View Full Program</a></p>
@endsection

@section('connect')
    <h2>Friend Requests</h2>
    @auth
        @if(isset($pendingRequests) && $pendingRequests->count())
            <ul>
                @foreach($pendingRequests as $r)
                    <li>
                        <strong>{{ $r->sender->name ?? 'Unknown' }}</strong>
                        <div>{{ Str::limit($r->message ?? '', 120) }}</div>
                        <div>
                            <p><a href="{{ route('connect.index') }}">Manage requests</a></p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p>No pending friend requests.</p>
        @endif

        <p><a href="{{ route('connect.index') }}">Open Connect</a></p>
    @endauth

    @guest
        <p>
            <a href="{{ route('login') }}">Log in</a> or <a href="{{ route('register') }}">Register</a> to connect with others
        </p>
    @endguest
@endsection

@section('resources')
    <h2>Newest Resources</h2>
    <ul>
        @forelse($latestResources ?? [] as $resource)
            <li>
                <!-- resource card link wraps the item so the entire element is clickable -->
                <a href="{{ route('resources.show', $resource->id) }}" class="resource-card">
                    @if(!empty($resource->image))
                        <p>
                            <img src="{{ Str::startsWith($resource->image, 'images/') ? asset('storage/' . $resource->image) : asset($resource->image) }}" alt="{{ $resource->title }}" class="rounded-img-small">
                        </p>
                    @endif
                    <strong>{{ $resource->title }}</strong>
                    <div>{{ Str::limit($resource->content, 150) }}</div>
                </a>
            </li>
        @empty
            <li>
                <p>No resources yet.</p>
            </li>
        @endforelse
    </ul>
    <p><a href="{{ route('resources.index') }}">See All Resources</a></p>
@endsection
