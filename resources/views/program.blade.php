@extends('layouts.app')

@section('title', 'Weekly Program')

@section('content')
    @can('admin')
        <p><a href="{{ route('program.manage') }}">Manage program</a></p>
    @endcan

    @if($programs->isEmpty())
        <p>No program items available.</p>
    @else
        <div class="program-list">
            @foreach($programs as $item)
                <article class="program-item">
                    <h2>{{ $item->title }}</h2>
                    <p>
                        {{ $item->day_of_week ? $item->day_of_week . ' — ' : '' }}

                        @if($item->start_time)
                            {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                        @endif

                        @if($item->end_time)
                            - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                        @endif
                    </p>

                    @if($item->description)
                        <p>{{ $item->description }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
@endsection
