@extends('layouts.app')

@section('title', 'Resources')

@section('admin')
    <a href="{{ route('resources.create') }}">Add Resource</a>
@endsection

@section('content')
    @foreach($posts as $post)
        <article>
            <h2>{{ $post->title }}</h2>
            <h3>{{ $post->author }}</h3>
            <img src="{{ Str::startsWith($post->image, 'default-news-image') ? asset('images/' . $post->image) : asset('storage/' . $post->image) }}" alt="img">
            <p>{{ $post->content }}</p>
            <p>Published on {{ $post->published_at }}</p>

            @if(\App\Helpers\isAdmin())
                <a href="{{ route('resources.edit', $post->id) }}">Edit</a>
                <form action="{{ route('resources.destroy', $post->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            @endif

        </article>
    @endforeach
@endsection
