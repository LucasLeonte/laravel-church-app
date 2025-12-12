@php use Carbon\Carbon; @endphp
<div class="resource-comment" style="padding: 1.5rem 0; border-bottom: 1px solid var(--border-color);">
    <div style="display:flex; justify-content:space-between; align-items: flex-start; margin-bottom: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            {{-- Avatar Placeholder --}}
            <div style="width: 40px; height: 40px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-dark); font-weight: bold; font-size: 1.1rem;">
                {{ substr($comment->user->name ?? 'U', 0, 1) }}
            </div>
            
            <div>
                <strong style="display: block; color: var(--text-main); font-size: 1rem;">{{ $comment->user->name ?? 'Unknown' }}</strong>
                <span style="color: var(--text-muted); font-size: 0.85rem;">{{ $comment->created_at ? Carbon::parse($comment->created_at)->diffForHumans() : '' }}</span>
            </div>
        </div>

        @if(auth()->check() && (auth()->id() === $comment->user_id || (auth()->user()->is_admin ?? false)))
            <form method="POST" action="{{ route('resources.comments.destroy', ['resource' => $comment->resource_id, 'comment' => $comment->id]) }}" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:none; border:none; color: #E63946; font-size: 0.85rem; font-weight: 600; cursor:pointer; padding: 0.25rem 0.5rem; border-radius: 4px; transition: background 0.2s;">
                    Delete
                </button>
            </form>
        @endif
    </div>

    <div style="color: var(--text-main); padding-left: 3.5rem; line-height: 1.6;">
        {!! nl2br(e($comment->body)) !!}
    </div>
</div>
