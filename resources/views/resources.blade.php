@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'Resources')

@section('admin-header')
    <a href="{{ route('resources.create') }}">Add Resource</a>
@endsection

@section('content')
    @foreach($resources as $post)
        <article>
            <h2>{{ $post->title }}</h2>
            <h3>{{ $post->author }}</h3>
            {{-- determine whether to load from public/images (default) or storage --}}
            <img
                src="{{ Str::startsWith($post->image, 'default-resources-image') ? asset('images/' . $post->image) : asset('storage/' . $post->image) }}"
                alt="img">
            <p>{{ $post->content }}</p>

            @if(!empty($post->link))
                <p>Link: <a href="{{ $post->link }}" target="_blank" rel="noopener noreferrer">{{ $post->link }}</a></p>
            @endif

            <p>Published on {{ $post->published_at ? Carbon::parse($post->published_at)->toFormattedDateString() : $post->published_at }}</p>

            @can('admin')
                <a href="{{ route('resources.edit', $post->id) }}">Edit</a>
                <form action="{{ route('resources.destroy', $post->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            @endcan

        </article>
    @endforeach
@endsection
