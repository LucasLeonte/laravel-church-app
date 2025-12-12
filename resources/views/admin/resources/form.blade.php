@extends('layouts.app')

@section('title', $post->exists ? 'Edit Resources Post' : 'Create Resources Post')

@section('content')
    <form method="POST" action="{{ $post->exists ? route('resources.update', $post->id) : route('resources.store') }}" enctype="multipart/form-data" class="form-card">
        @csrf

        @if($post->exists)
            @method('PUT')
        @endif

        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required>

        <label for="author">Author</label>
        <input type="text" name="author" id="author" value="{{ old('author', $post->author) }}" required>

        <label for="resource_category_id">Category</label>
        <select name="resource_category_id" id="resource_category_id">
            <option value="">-- None --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (int)old('resource_category_id', $post->resource_category_id) === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>

        <label for="content">Content</label>
        <textarea name="content" id="content">{{ old('content', $post->content) }}</textarea>

        <label for="image">Image</label>
        <input type="file" name="image" id="image" accept="image/*">
        @if($post->image)
            <div>
                {{-- show default public image when filename indicates default, otherwise load from storage --}}
                <img src="{{ Str::startsWith($post->image, 'default-resources-image') ? asset('images/' . $post->image) : asset('storage/' . $post->image) }}" alt="Current image" style="max-width: 150px;">
            </div>
        @endif

        <label for="link">Web link (optional)</label>
        <input type="url" name="link" id="link" value="{{ old('link', $post->link) }}" placeholder="https://example.com">
        @if($post->link)
            <div>
                Current link: <a href="{{ $post->link }}" target="_blank" rel="noopener noreferrer">{{ $post->link }}</a>
            </div>
        @endif

        <button type="submit" class="form-submit-button">{{ $post->exists ? 'Update' : 'Create' }}</button>
    </form>
@endsection
