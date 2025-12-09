@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'News')

@section('admin-header')
    <a href="{{ route('news.create') }}">Add News Post</a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @foreach($news as $post)
        <article>
            <h2>{{ $post->title }}</h2>

            @if(!empty($post->author))
                <p>By {{ $post->author }}</p>
            @endif

            <img
                src="{{ Str::startsWith($post->image, 'default-news-image') ? asset('images/' . $post->image) : asset('storage/' . $post->image) }}"
                alt="img">
            <p>{{ $post->content }}</p>
            <p>Published on {{ $post->published_at ? Carbon::parse($post->published_at)->toFormattedDateString() : $post->published_at }}</p>

            @can('admin')
                <a href="{{ route('news.edit', $post->id) }}">Edit</a>
                <form action="{{ route('news.destroy', $post->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this news post?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            @endcan

        </article>
    @endforeach
@endsection
