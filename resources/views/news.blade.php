@extends('layouts.app')

@section('title', 'News')

@section('admin-header')
    <a href="{{ route('news.create') }}">Add News Post</a>
@endsection

@section('content')
    @foreach($posts as $post)
        <article>
            <h2>{{ $post->title }}</h2>
            <img src="{{ Str::startsWith($post->image, 'default-news-image') ? asset('images/' . $post->image) : asset('storage/' . $post->image) }}" alt="img">
            <p>{{ $post->content }}</p>
            <p>Published on {{ $post->published_at }}</p>

            @can('admin')
                <a href="{{ route('news.edit', $post->id) }}">Edit</a>
                <form action="{{ route('news.destroy', $post->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            @endcan

        </article>
    @endforeach
@endsection
