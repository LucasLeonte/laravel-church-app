@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', 'News')

@section('admin-header')
    <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
        <a href="{{ route('news.create') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem;">
             <span>+</span> Add News Post
        </a>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success" style="background: var(--primary-light); color: white; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 2rem;">{{ session('success') }}</div>
    @endif

    <section class="dashboard-section section-news">
        <ul class="grid-auto">
        @foreach($news as $post)
            <li style="position: relative; overflow: hidden; display: flex; flex-direction: column;">
                <div style="height: 200px; overflow: hidden; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                    <img
                        src="{{ Str::startsWith($post->image, 'default-news-image') ? asset('images/' . $post->image) : asset('storage/' . $post->image) }}"
                        alt="img"
                        style="width: 100%; height: 100%; object-fit: cover;"
                    >
                </div>

                <h2>{{ $post->title }}</h2>

                @if(!empty($post->author))
                    <p style="font-size: 0.85rem; color: var(--text-muted);">By {{ $post->author }} | {{ $post->published_at ? Carbon::parse($post->published_at)->toFormattedDateString() : $post->published_at }}</p>
                @endif

                <p style="flex-grow: 1;">{{ Str::limit($post->content, 120) }}</p>

                @can('admin')
                    <div style="border-top: 1px dashed var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #f8faf8; margin: 1.5rem -1.5rem -1.5rem;padding: 1rem 1.5rem;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Admin</span>

                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <a href="{{ route('news.edit', $post->id) }}" style="font-size: 0.9rem; font-weight: 600; color: var(--primary); padding: 0.25rem 0.5rem; border-radius: 4px; background: white; border: 1px solid var(--border-color);">
                                &#9998; Edit
                            </a>

                            <form action="{{ route('news.destroy', $post->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this news post?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #E63946; font-size: 0.9rem; font-weight: 600; padding: 0.25rem 0.5rem; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                    &#128465; Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endcan
            </li>
        @endforeach
        </ul>
    </section>
@endsection
