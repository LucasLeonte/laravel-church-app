@extends('layouts.app')

@section('title', 'News')

@section('admin')
    <a href="{{ route('news.create') }}">Add News</a>
@endsection

@section('content')
    @foreach($newsPosts as $post)
        <article>
            <h2>{{ $post->title }}</h2>
            <img src="{{ asset('images/' . $post->image) }}" alt="img">
            <p>{{ $post->content }}</p>
            <p>Published on {{ $post->published_at }}</p>

            @if(\App\Helpers\isAdmin())
                <a href="{{ route('news.edit', $post->id) }}">Edit</a>
                <form action="{{ route('news.destroy', $post->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            @endif

        </article>
    @endforeach
@endsection
