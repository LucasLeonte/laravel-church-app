@extends('layouts.app')

@section('title', $category->exists ? 'Edit Category' : 'Create Category')

@section('content')
    <form method="POST" action="{{ $category->exists ? route('resources.categories.update', $category->id) : route('resources.categories.store') }}" class="form-card">
        @csrf
        @if($category->exists)
            @method('PUT')
        @endif
        <div>
            <label>Name</label>
            <input name="name" value="{{ old('name', $category->name) }}" required>
        </div>
        <div>
            <label>Description</label>
            <textarea name="description">{{ old('description', $category->description) }}</textarea>
        </div>
        <button type="submit">Save</button>
    </form>
@endsection

