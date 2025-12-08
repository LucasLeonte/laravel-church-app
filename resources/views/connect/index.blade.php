@php use Carbon\Carbon;use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Connect</h1>

        @if(session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-800">{{ session('status') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($users as $user)
                <div class="border rounded p-4 flex gap-4 items-start">
                    <div class="w-16 h-16 flex-shrink-0">
                        @if($user->avatar)
                            <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="{{ $user->name }} avatar"
                                 class="w-16 h-16 rounded-full object-cover">
                        @else
                            <img src="{{ asset('images/default-avatar.svg') }}" alt="default avatar"
                                 class="w-16 h-16 rounded-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="font-semibold text-lg">{{ $user->name }}</h2>
                                @if($user->birthdate)
                                    <div class="text-sm text-gray-600">
                                        Birthdate: {{ Carbon::parse($user->birthdate)->toFormattedDateString() }}</div>
                                @endif
                            </div>
                            <div class="text-sm">
                                @php $status = $statusMap[$user->id] ?? null; @endphp
                                @if(! auth()->check())
                                    <a href="{{ route('login') }}" class="text-blue-600">Sign in to connect</a>
                                @else
                                    @if($status === 'accepted')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Friends</span>
                                    @elseif($status === 'sent')
                                        @php $frId = $idMap[$user->id] ?? null; @endphp
                                        <form action="{{ route('connect.request.cancel', ['id' => $frId]) }}"
                                              method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Cancel
                                                Request
                                            </button>
                                        </form>
                                    @elseif($status === 'received')
                                        @php $frId = $idMap[$user->id] ?? null; @endphp
                                        <form action="{{ route('connect.request.accept', ['id' => $frId]) }}"
                                              method="POST" style="display:inline">
                                            @csrf
                                            <button class="px-2 py-1 bg-green-100 text-green-800 rounded">Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('connect.request.decline', ['id' => $frId]) }}"
                                              method="POST" style="display:inline">
                                            @csrf
                                            <button class="px-2 py-1 bg-red-100 text-red-800 rounded">Decline</button>
                                        </form>
                                    @else
                                        <form action="{{ route('connect.request.send', ['receiver' => $user->id]) }}"
                                              method="POST">
                                            @csrf
                                            <button class="px-2 py-1 bg-blue-600 text-white rounded">Add Friend</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if($user->bio)
                            <p class="mt-2 text-sm text-gray-700">{{ Str::limit($user->bio, 150) }}</p>
                        @endif

                        @if($user->favoriteVerses && $user->favoriteVerses->count())
                            <div class="mt-3 text-sm">
                                <strong>Favorite verses:</strong>
                                <ul class="list-disc ml-5">
                                    @foreach($user->favoriteVerses as $fv)
                                        <li>{{ $fv->translation }} {{ $fv->book }} {{ $fv->chapter }}
                                            :{{ $fv->verse }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
@endsection
