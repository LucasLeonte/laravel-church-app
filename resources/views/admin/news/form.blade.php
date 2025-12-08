@extends('layouts.app')

@section('title', $post->exists ? 'Edit News Post' : 'Create News Post')

@section('content')
    <form method="POST" action="{{ $post->exists ? route('news.update', $post->id) : route('news.store') }}" enctype="multipart/form-data">
        @csrf

        @if($post->exists)
            @method('PUT')
        @endif

        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required>

        <label for="content">Content</label>
        <textarea name="content" id="content" required>{{ old('content', $post->content) }}</textarea>

        <label for="image">Image</label>
        <input type="file" name="image" id="image" accept="image/*" {{ $post->exists ? '' : 'required' }}>
        @if($post->image)
            <div>
                <img src="{{ asset('storage/' . $post->image) }}" alt="Current image" style="max-width: 150px;">
            </div>
        @endif

        <button type="submit" class="form-submit-button">{{ $post->exists ? 'Update' : 'Create' }}</button>
    </form>
@endsection
