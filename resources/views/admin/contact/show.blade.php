@extends('layouts.app')

@section('title', 'Contact message')

@section('content')
    <section class="dashboard-section" style="max-width: 800px; margin: 0 auto;">
        <div class="admin-header">
            <h2>View Message</h2>
            <a href="{{ route('admin.contact.index') }}" class="btn" style="background: var(--secondary); color: var(--primary-dark);">
                &larr; Back to List
            </a>
        </div>

        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); background: #f8faf8; display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem;">{{ $message->name }}</h3>
                    <div style="color: var(--text-muted); font-size: 0.95rem;">
                        <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                    </div>
                </div>
                <div style="text-align: right; color: var(--text-muted); font-size: 0.9rem;">
                    {{ $message->created_at->format('M d, Y') }}<br>
                    {{ $message->created_at->format('h:i A') }}
                </div>
            </div>
            
            <div style="padding: 2rem; color: var(--text-main); line-height: 1.7;">
                {!! nl2br(e($message->message)) !!}
            </div>

            @if($message->replied_at)
                <div style="padding: 1.5rem; background: #f0fdf4; border-top: 1px solid #bbf7d0;">
                    <h4 style="color: var(--primary); margin-bottom: 0.5rem; font-size: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">Your Reply</h4>
                    <p style="margin-bottom: 0.5rem;">{!! nl2br(e($message->reply_message)) !!}</p>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        Sent on {{ $message->replied_at->format('M d, Y \a\t h:i A') }}
                    </div>
                </div>
            @endif
        </div>

        @if(!$message->replied_at)
            <div class="form-card" style="margin: 0; max-width: 100%;">
                <h3 style="margin-bottom: 1.5rem;">Reply to Message</h3>
                
                @if($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 1.5rem; background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 1rem; border-radius: var(--radius-sm);">
                        <ul style="margin: 0; padding-left: 1rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.contact.reply', $message) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="reply_message">Your Reply</label>
                        <textarea id="reply_message" name="reply_message" rows="6" required style="width: 100%;">{{ old('reply_message') }}</textarea>
                    </div>
                    <div style="text-align: right;">
                        <button type="submit" class="btn">Send Reply</button>
                    </div>
                </form>
            </div>
        @endif
        
        <div style="text-align: center; margin-top: 2rem;">
            <form action="{{ route('admin.contact.destroy', $message) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: transparent; border: none; color: #E63946; font-weight: 600; cursor: pointer; text-decoration: underline;">
                    Delete this message permanently
                </button>
            </form>
        </div>
    </section>
@endsection
