@extends('layouts.app')

@section('title', 'Manage Resources')

@section('content')
    <a href="{{ route('resources.create') }}">Create new Resource</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul>
        @foreach($resources as $resource)
            <li>
                <strong>{{ $resource->title }}</strong> (Category: {{ $resource->category->name ?? 'None' }})
                <a href="{{ route('resources.edit', $resource->id) }}">Edit</a>
                <form action="{{ route('resources.destroy', $resource->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this resource?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
                <div>{{ Str::limit($resource->content, 300) }}</div>
                @if(!empty($resource->link))
                    <div>Link: <a href="{{ $resource->link }}" target="_blank" rel="noopener noreferrer">{{ $resource->link }}</a></div>
                @endif
            </li>
        @endforeach
    </ul>

    @if(method_exists($resources, 'links'))
        {{ $resources->links() }}
    @endif
@endsection
