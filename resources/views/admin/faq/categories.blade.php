@extends('layouts.app')

@section('title', 'Manage FAQ Categories')

@section('content')
    <a href="{{ route('faq.categories.create') }}">Create new category</a>
    <ul>
        @foreach($categories as $category)
            <li>
                {{ $category->name }}
                <a href="{{ route('faq.categories.edit', $category->id) }}">Edit</a>
                <form action="{{ route('faq.categories.destroy', $category->id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection

