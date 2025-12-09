@php use Carbon\Carbon; use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('title', 'Connect with Others')

@section('content')
    {{-- Search bar --}}
    <form method="GET" action="{{ route('connect.index') }}">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Search people by name or bio..." />
        <button>Search</button>
    </form>

    @if(session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if(auth()->check() && isset($friends))
        {{-- Friends section --}}
        <section>
            <h2>Friends</h2>
            @if($friends && $friends->count())
                @foreach($friends as $user)
                    <article>
                        @if($user->avatar)
                            <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="{{ $user->name }} avatar">
                        @else
                            <img src="{{ asset('images/default-avatar.svg') }}" alt="default avatar">
                        @endif

                        <header>
                            <h3>{{ $user->name }}</h3>
                            @if($user->birthdate)
                                <div>Birthdate: {{ Carbon::parse($user->birthdate)->toFormattedDateString() }}</div>
                            @endif
                        </header>

                        <div>
                            <span>Friends</span>
                            <form action="{{ route('connect.friend.remove', ['other' => $user->id]) }}" method="POST" onsubmit="return confirm('Remove this friend?');">
                                @csrf
                                <button type="submit">Remove</button>
                            </form>
                        </div>

                        @if($user->bio)
                            <p>{{ Str::limit($user->bio, 150) }}</p>
                        @endif

                        @if($user->favoriteVerses && $user->favoriteVerses->count())
                            <div>
                                <strong>Favorite verses:</strong>
                                <ul>
                                    @foreach($user->favoriteVerses as $fv)
                                        <li>{{ $fv->translation }} {{ $fv->book }} {{ $fv->chapter }}:{{ $fv->verse }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </article>
                @endforeach
            @else
                <p>You don't have any friends yet.</p>
            @endif
        </section>

        {{-- Received friend requests --}}
        <section>
            <h2>Friend requests</h2>
            @if($received && $received->count())
                @foreach($received as $user)
                    @php $frId = $receivedMap[$user->id] ?? null; @endphp
                    <article>
                        @if($user->avatar)
                            <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="{{ $user->name }} avatar">
                        @else
                            <img src="{{ asset('images/default-avatar.svg') }}" alt="default avatar">
                        @endif

                        <header>
                            <h3>{{ $user->name }}</h3>
                            @if($user->birthdate)
                                <div>Birthdate: {{ Carbon::parse($user->birthdate)->toFormattedDateString() }}</div>
                            @endif
                        </header>

                        <div>
                            <form action="{{ route('connect.request.accept', ['id' => $frId]) }}" method="POST">
                                @csrf
                                <button>Accept</button>
                            </form>

                            <form action="{{ route('connect.request.decline', ['id' => $frId]) }}" method="POST">
                                @csrf
                                <button>Decline</button>
                            </form>
                        </div>

                        @if($user->bio)
                            <p>{{ Str::limit($user->bio, 150) }}</p>
                        @endif

                        @if($user->favoriteVerses && $user->favoriteVerses->count())
                            <div>
                                <strong>Favorite verses:</strong>
                                <ul>
                                    @foreach($user->favoriteVerses as $fv)
                                        <li>{{ $fv->translation }} {{ $fv->book }} {{ $fv->chapter }}:{{ $fv->verse }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </article>
                @endforeach
            @else
                <p>No new friend requests.</p>
            @endif
        </section>

        {{-- Other users --}}
        <section>
            <h2>Other people</h2>
            @foreach($others as $user)
                <article>
                    @if($user->avatar)
                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="{{ $user->name }} avatar">
                    @else
                        <img src="{{ asset('images/default-avatar.svg') }}" alt="default avatar">
                    @endif

                    <header>
                        <h3>{{ $user->name }}</h3>
                        @if($user->birthdate)
                            <div>Birthdate: {{ Carbon::parse($user->birthdate)->toFormattedDateString() }}</div>
                        @endif
                    </header>

                    <div>
                        @php $status = $statusMap[$user->id] ?? null; @endphp
                        @if($status === 'accepted')
                            <span>Friends</span>
                        @elseif($status === 'sent')
                            @php $frId = $idMap[$user->id] ?? null; @endphp
                            <form action="{{ route('connect.request.cancel', ['id' => $frId]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button>Cancel Request</button>
                            </form>
                        @elseif($status === 'received')
                            @php $frId = $idMap[$user->id] ?? null; @endphp
                            <form action="{{ route('connect.request.accept', ['id' => $frId]) }}" method="POST">
                                @csrf
                                <button>Accept</button>
                            </form>
                            <form action="{{ route('connect.request.decline', ['id' => $frId]) }}" method="POST">
                                @csrf
                                <button>Decline</button>
                            </form>
                        @else
                            <form action="{{ route('connect.request.send', ['receiver' => $user->id]) }}" method="POST">
                                @csrf
                                <button>Add Friend</button>
                            </form>
                        @endif
                    </div>

                    @if($user->bio)
                        <p>{{ Str::limit($user->bio, 150) }}</p>
                    @endif

                    @if($user->favoriteVerses && $user->favoriteVerses->count())
                        <div>
                            <strong>Favorite verses:</strong>
                            <ul>
                                @foreach($user->favoriteVerses as $fv)
                                    <li>{{ $fv->translation }} {{ $fv->book }} {{ $fv->chapter }}:{{ $fv->verse }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </article>
            @endforeach

            {{ $others->links() }}
        </section>

    @else
        {{-- Guest listing --}}
        <section>
            @foreach($users as $user)
                <article>
                    @if($user->avatar)
                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="{{ $user->name }} avatar">
                    @else
                        <img src="{{ asset('images/default-avatar.svg') }}" alt="default avatar">
                    @endif

                    <header>
                        <h3>{{ $user->name }}</h3>
                        @if($user->birthdate)
                            <div>Birthdate: {{ Carbon::parse($user->birthdate)->toFormattedDateString() }}</div>
                        @endif
                    </header>

                    <div>
                        <a href="{{ route('login') }}">Sign in to connect</a>
                    </div>

                    @if($user->bio)
                        <p>{{ Str::limit($user->bio, 150) }}</p>
                    @endif

                    @if($user->favoriteVerses && $user->favoriteVerses->count())
                        <div>
                            <strong>Favorite verses:</strong>
                            <ul>
                                @foreach($user->favoriteVerses as $fv)
                                    <li>{{ $fv->translation }} {{ $fv->book }} {{ $fv->chapter }}:{{ $fv->verse }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </article>
            @endforeach

            {{ $users->links() }}
        </section>
    @endif
@endsection
