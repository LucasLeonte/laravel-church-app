@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('title', $resource->title ?? 'Resource')
@section('hide_title', true)

@section('content')
    <div style="max-width: 900px; margin: 0 auto;">
        
        {{-- Back Link --}}
        <div style="margin-bottom: 1rem;">
            <a href="{{ route('resources.index') }}" class="btn" style="background: var(--secondary); color: var(--primary-dark); display: inline-flex; align-items: center; gap: 0.5rem;">
                &larr; Back to Resources
            </a>
        </div>

        <section class="dashboard-section" style="padding: 2rem;">
            
            {{-- Header: Title & Meta --}}
            <header style="text-align: center; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem;">
                @if(!empty($resource->resource_category_id) && $resource->category)
                    <span style="display: inline-block; background: var(--secondary); color: var(--primary-dark); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem;">
                        {{ $resource->category->name }}
                    </span>
                @endif
                
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-main);">{{ $resource->title }}</h1>
                
                <div style="color: var(--text-muted); font-size: 0.95rem;">
                    By <span style="font-weight: 600; color: var(--primary-dark);">{{ $resource->author }}</span>
                    &bull; 
                    {{ $resource->published_at ? Carbon::parse($resource->published_at)->toFormattedDateString() : 'Draft' }}
                </div>
            </header>

            {{-- Featured Image --}}
            @if(!empty($resource->image))
                <div style="text-align: center; margin-bottom: 2.5rem;">
                    <img
                        src="{{ Str::startsWith($resource->image, 'default-resources-image') ? asset('images/' . $resource->image) : asset('storage/' . $resource->image) }}"
                        alt="{{ $resource->title }}" 
                        style="max-width: 100%; height: auto; max-height: 500px; border-radius: var(--radius-md); box-shadow: var(--shadow-md); object-fit: cover;"
                    >
                </div>
            @endif

            {{-- Main Content --}}
            <div style="font-size: 1.1rem; line-height: 1.8; color: var(--text-main); margin-bottom: 3rem;">
                {!! $resource->content !!}
            </div>

            @if(!empty($resource->link))
                <div style="background: var(--bg-body); padding: 1.5rem; border-radius: var(--radius-sm); border-left: 4px solid var(--highlight); margin-bottom: 3rem;">
                    <strong>External Link:</strong> 
                    <a href="{{ $resource->link }}" target="_blank" rel="noopener noreferrer" style="color: var(--primary); text-decoration: underline;">
                        {{ $resource->link }}
                    </a>
                </div>
            @endif

            {{-- Comments Section --}}
            <div id="comments" style="padding-top: 2rem; border-top: 1px dashed var(--border-color);">
                <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Comments ({{ $resource->comments->count() }})</h3>

                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
                @endif

                <div class="comments-list" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @forelse($resource->comments as $comment)
                        @include('resources._comment', ['comment' => $comment])
                    @empty
                        <p style="color: var(--text-muted); font-style: italic;">No comments yet. Be the first to share your thoughts!</p>
                    @endforelse
                </div>

                {{-- Comment Form --}}
                @auth
                    <form id="commentForm" method="POST" action="{{ route('resources.comments.store', ['resource' => $resource->id]) }}" class="form-card" style="margin: 2rem 0 0 0; padding: 1.5rem; box-shadow: none; background: var(--bg-body); border: none;">
                        @csrf
                        <div class="form-group">
                            <label for="body" style="margin-bottom: 0.5rem; display: block;">Leave a comment</label>
                            <textarea name="body" id="body" rows="4" style="width:100%;" required maxlength="1000" placeholder="Type your comment here...">{{ old('body') }}</textarea>
                            @error('body')
                                <div style="color: #E63946; font-size: 0.9rem; margin-top: 0.5rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn">Post Comment</button>
                    </form>
                @else
                    <p style="margin-top: 2rem; padding: 1rem; background: var(--bg-body); border-radius: var(--radius-sm); text-align: center;">
                        <a href="{{ route('login') }}" style="font-weight: 600; text-decoration: underline;">Log in</a> to leave a comment.
                    </p>
                @endauth
            </div>

        </section>
    </div>

    {{-- Keep the script, it's useful --}}
    <script>
        (function () {
            const form = document.getElementById('commentForm');
            if (!form) return;
            form.addEventListener('submit', function (e) {
                const textarea = document.getElementById('body');
                if (textarea) textarea.value = textarea.value.trim();

                const val = textarea ? textarea.value : '';
                let ok = true;
                if (!val) { ok = false; showError(textarea, 'Please enter a comment'); }
                else if (val.length > 1000) { ok = false; showError(textarea, 'Comment is too long'); }

                if (!ok) {
                    e.preventDefault();
                }

                function showError(el, message) {
                    clearError(el);
                    const d = document.createElement('div');
                    d.className = 'client-error text-danger';
                    d.style.color = '#E63946';
                    d.style.marginTop = '0.5rem';
                    d.style.fontSize = '0.9rem';
                    d.textContent = message;
                    el.parentNode.insertBefore(d, el.nextSibling);
                }

                function clearError(el) {
                    const next = el.nextElementSibling;
                    if (next && next.classList.contains('client-error')) next.remove();
                }
            });
        })();
    </script>
@endsection
