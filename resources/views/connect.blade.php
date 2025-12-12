@php use Carbon\Carbon; use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('title', 'Connect with Others')

@section('content')
    {{-- Search bar --}}
    <section class="dashboard-section">
        <form method="GET" action="{{ route('connect.index') }}">
            <div class="form-group">
                <label>Find Friends</label>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search people by name or bio..." />
            </div>
            <button class="btn">Search</button>
        </form>
    </section>

    @if(session('status'))
        <div style="background: var(--primary-light); color: white; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 2rem;">
            {{ session('status') }}
        </div>
    @endif

    @if(auth()->check() && isset($friends))
        {{-- Friends section --}}
        <section class="dashboard-section section-connect">
            <h2>Friends</h2>
            @if($friends && $friends->count())
                <ul>
                @foreach($friends as $user)
                    <li>
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; margin-bottom: 1rem; text-align: center;">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }} avatar" class="rounded-img-small" style="margin:0;">
                            @else
                                <img src="{{ asset('images/default-avatar.svg') }}" alt="default avatar" class="rounded-img-small" style="margin:0;">
                            @endif
                            <div>
                                <strong style="display: block;">{{ $user->name }}</strong>
                                @if($user->birthdate)
                                    <div style="font-size: 0.85rem; color: var(--text-muted);">{{ Carbon::parse($user->birthdate)->toFormattedDateString() }}</div>
                                @endif
                            </div>
                        </div>

                        @if($user->bio)
                            <p style="font-size: 0.9rem;">{{ Str::limit($user->bio, 80) }}</p>
                        @endif

                        <div style="margin-top: auto;">
                            <form action="{{ route('connect.friend.remove', ['other' => $user->id]) }}" method="POST" onsubmit="return confirm('Remove this friend?');" class="inline-form">
                                @csrf
                                <button type="submit">Remove Friend</button>
                            </form>
                        </div>
                    </li>
                @endforeach
                </ul>
            @else
                <p>You don't have any friends yet.</p>
            @endif
        </section>

        {{-- Received friend requests --}}
        <section class="dashboard-section section-connect">
            <h2>Friend requests</h2>
            @if($received && $received->count())
                <ul>
                @foreach($received as $user)
                    @php $frId = $receivedMap[$user->id] ?? null; @endphp
                    <li>
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; margin-bottom: 1rem; text-align: center;">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }} avatar" class="rounded-img-small" style="margin:0;">
                            @else
                                <img src="{{ asset('images/default-avatar.svg') }}" alt="default avatar" class="rounded-img-small" style="margin:0;">
                            @endif
                            <div>
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </div>

                        <div style="margin-top: auto;">
                            <form action="{{ route('connect.request.accept', ['id' => $frId]) }}" method="POST" class="inline-form">
                                @csrf
                                <button style="background: var(--primary);">Accept</button>
                            </form>

                            <form action="{{ route('connect.request.decline', ['id' => $frId]) }}" method="POST" class="inline-form">
                                @csrf
                                <button style="background: #E63946;">Decline</button>
                            </form>
                        </div>
                    </li>
                @endforeach
                </ul>
            @else
                <p>No new friend requests.</p>
            @endif
        </section>

        {{-- Other users --}}
        <section class="dashboard-section section-connect">
            <h2>Other people</h2>
            <ul class="grid-auto"> <!-- Using auto grid here -->
            @foreach($others as $user)
                <li>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; margin-bottom: 1rem; text-align: center;">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }} avatar" class="rounded-img-small" style="margin:0;">
                        @else
                            <img src="{{ asset('images/default-avatar.svg') }}" alt="default avatar" class="rounded-img-small" style="margin:0;">
                        @endif
                        <strong>{{ $user->name }}</strong>
                    </div>

                    @if($user->bio)
                        <p style="font-size: 0.9rem; margin-bottom: 1rem;">{{ Str::limit($user->bio, 80) }}</p>
                    @endif

                    <div style="margin-top: auto;">
                        @php $status = $statusMap[$user->id] ?? null; @endphp
                        @if($status === 'accepted')
                            <span style="color: var(--primary); font-weight: bold;">Friends</span>
                        @elseif($status === 'sent')
                            @php $frId = $idMap[$user->id] ?? null; @endphp
                            <form action="{{ route('connect.request.cancel', ['id' => $frId]) }}" method="POST" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button>Cancel Request</button>
                            </form>
                        @elseif($status === 'received')
                            <span style="font-size: 0.9rem; color: var(--highlight);">Request Received</span>
                        @else
                            <form action="{{ route('connect.request.send', ['receiver' => $user->id]) }}" method="POST" class="inline-form">
                                @csrf
                                <button>Add Friend</button>
                            </form>
                        @endif
                    </div>
                </li>
            @endforeach
            </ul>

            <div style="margin-top: 2rem;">
                {{ $others->links() }}
            </div>
        </section>

    @else
        {{-- Guest listing --}}
        <section class="dashboard-section section-connect">
            <ul class="grid-auto">
            @foreach($users as $user)
                <li>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; margin-bottom: 1rem; text-align: center;">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }} avatar" class="rounded-img-small" style="margin:0;">
                        @else
                            <img src="{{ asset('images/default-avatar.svg') }}" alt="default avatar" class="rounded-img-small" style="margin:0;">
                        @endif
                        <strong>{{ $user->name }}</strong>
                    </div>
                    
                    <div>
                        <a href="{{ route('login') }}">Sign in to connect</a>
                    </div>
                </li>
            @endforeach
            </ul>
            <div style="margin-top: 2rem;">
                {{ $users->links() }}
            </div>
        </section>
    @endif
@endsection
