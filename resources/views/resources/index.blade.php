@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'Resources')

@section('content')
    @can('admin')
        <section class="dashboard-section" style="background: #f8faf8; padding: 1rem 1.5rem; border-radius: var(--radius-md); border: 1px dashed var(--border-color); display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <span style="font-weight: 700; color: var(--text-muted); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em;">Admin Controls</span>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <a href="{{ route('resources.create') }}" class="btn" style="padding: 0.4rem 1rem; font-size: 0.9rem;">+ Create Resource</a>
                <a href="{{ route('resources.manage') }}" style="font-size: 0.9rem; color: var(--primary-dark); font-weight: 500; display: inline-flex; align-items: center;">Manage Resources</a>
                <span style="border-left: 1px solid var(--border-color); height: 1.5rem; margin: 0 0.5rem;"></span>
                <a href="{{ route('resources.categories.create') }}" style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500; display: inline-flex; align-items: center;">+ Create Category</a>
                <a href="{{ route('resources.categories.index') }}" style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500; display: inline-flex; align-items: center;">Manage Categories</a>
            </div>
        </section>
    @endcan

    {{-- Search and filters --}}
    <section class="dashboard-section">
        <div style="background: var(--bg-surface); padding: 2rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem; border-bottom: none;">Find Resources</h2>
            <form method="GET" action="{{ route('resources.index') }}">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    
                    {{-- Search Input --}}
                    <div style="flex-grow: 2; min-width: 250px;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-dark);">Search</label>
                        <input type="search" name="search" placeholder="Title or content..." value="{{ request('search') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm);" />
                    </div>
                    
                    {{-- Category Select --}}
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-dark);">Category</label>
                        <select name="category" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background-color: #fff;">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Author Select --}}
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-dark);">Author</label>
                        <select name="author" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background-color: #fff;">
                            <option value="">All Authors</option>
                            @foreach($authors as $a)
                                <option value="{{ $a }}" {{ request('author') == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; align-items: center;">
                    <button type="submit" class="btn" style="padding: 0.75rem 2rem;">Filter Resources</button>
                    @if(request('search') || request('category') || request('author'))
                        <a href="{{ route('resources.index') }}" style="color: var(--text-muted); font-weight: 500;">Clear Filters</a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="dashboard-section section-resources">
        <ul class="grid-auto">
        @foreach($resources as $post)
            <li tabindex="0" data-href="{{ route('resources.show', $post->id) }}" style="cursor:pointer; position: relative;">
                <div style="height: 180px; overflow: hidden; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                    <img 
                        src="{{ Str::startsWith($post->image, 'default-resources-image') ? asset('images/' . $post->image) : asset('storage/' . $post->image) }}" 
                        alt="{{ $post->title }}"
                        style="width: 100%; height: 100%; object-fit: cover;"
                    >
                </div>
                
                <h2>{{ $post->title }}</h2>
                <h3 style="font-size: 0.9rem; color: var(--primary); margin-top:0;">{{ $post->author }}</h3>

                <p>{{ Str::limit(strip_tags($post->content), 120) }}</p>
                
                <p style="margin-top: auto; font-size: 0.8rem; color: var(--text-muted);">
                    Published on {{ $post->published_at ? Carbon::parse($post->published_at)->toFormattedDateString() : $post->published_at }}
                </p>
            </li>
        @endforeach
        </ul>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('li[data-href]').forEach(function (el) {
                // click to navigate
                el.addEventListener('click', function () {
                    // if click came from a control that stopped propagation, nothing happens
                    window.location = el.dataset.href;
                });
                // keyboard support: Enter key
                el.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        window.location = el.dataset.href;
                    }
                });
            });
        });
    </script>

    @if(method_exists($resources, 'links'))
        <div style="margin-top: 2rem;">
            {{ $resources->links() }}
        </div>
    @endif
@endsection
