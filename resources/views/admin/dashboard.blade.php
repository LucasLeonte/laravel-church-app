@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('users')
    <div class="admin-header">
        <h2>Newest Users</h2>
        <a href="{{ route('admin.users.index') }}" class="btn">
            View All Users
        </a>
    </div>

    @if(isset($users) && $users->count())
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 30%">Name</th>
                        <th style="width: 40%">Email</th>
                        <th style="width: 20%">Joined</th>
                        <th class="actions" style="width: 10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td style="font-weight: 600;">{{ $u->name }}</td>
                            <td class="muted">{{ $u->email }}</td>
                            <td class="muted small">{{ $u->created_at->format('Y-m-d') }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.users.index') }}#user-{{ $u->id }}" style="font-weight: 600;">Manage</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="admin-table-wrapper" style="padding: 2rem; text-align: center; color: var(--text-muted);">
            No recent users.
        </div>
    @endif
@endsection

@section('news')
    @if(isset($latestNews))
        <div class="admin-header" style="margin-top: 3rem;">
            <h2>Latest News</h2>
            <div>
                <a href="{{ route('news.index') }}" class="btn" style="background: var(--secondary); color: var(--primary-dark); margin-right: 0.5rem;">View All</a>
                <a href="{{ route('news.create') }}" class="btn">+ New Post</a>
            </div>
        </div>

        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; display: flex; gap: 2rem; align-items: start;">
            @if(!empty($latestNews->image))
                <img src="{{ asset('storage/' . $latestNews->image) }}" alt="{{ $latestNews->title }}" style="width: 150px; height: 150px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
            @endif

            <div style="flex: 1;">
                <h3 style="margin-top: 0; font-size: 1.5rem;">{{ $latestNews->title }}</h3>
                <p style="color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem;">
                    {{ Str::limit($latestNews->content ?? $latestNews->body ?? '', 250) }}
                </p>
                <a href="{{ route('news.edit', $latestNews->id) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                    Edit Post
                </a>
            </div>
        </div>
    @else
        <div class="admin-header" style="margin-top: 3rem;">
            <h2>Latest News</h2>
            <a href="{{ route('news.create') }}" class="btn">+ New Post</a>
        </div>
        <div style="padding: 2rem; text-align: center; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
            No news posts yet.
        </div>
    @endif
@endsection

@section('program')
    <div style="margin-top: 3rem; background: var(--bg-surface); padding: 2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin-bottom: 0.5rem; border-bottom: none;">Program Management</h2>
            <p style="margin-bottom: 0;">Manage your weekly schedule and events.</p>
        </div>
        <a href="{{ route('program.manage') }}" class="btn">Manage Program</a>
    </div>
@endsection

@section('resources')
    <div style="margin-top: 2rem; background: var(--bg-surface); padding: 2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin-bottom: 0.5rem; border-bottom: none;">Resources Management</h2>
            <p style="margin-bottom: 0;">Manage documents, links, and categories.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('resources.categories.index') }}" class="btn" style="background: var(--secondary); color: var(--primary-dark);">Manage Categories</a>
            <a href="{{ route('resources.manage') }}" class="btn">Manage Resources</a>
        </div>
    </div>
@endsection

@section('faq')
    <div style="margin-top: 2rem; background: var(--bg-surface); padding: 2rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin-bottom: 0.5rem; border-bottom: none;">FAQ Management</h2>
            <p style="margin-bottom: 0;">Manage frequently asked questions.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('faq.categories.index') }}" class="btn" style="background: var(--secondary); color: var(--primary-dark);">Manage Categories</a>
            <a href="{{ route('faq.manage') }}" class="btn">Manage FAQs</a>
        </div>
    </div>
@endsection

@section('contact')
    <div class="admin-header" style="margin-top: 3rem;">
        <h2>Recent Contact Forms</h2>
        <a href="{{ route('admin.contact.index') }}" class="btn">
            View All Contact Forms
        </a>
    </div>

    @if(isset($messages) && $messages->count())
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 20%">Name</th>
                        <th style="width: 25%">Email</th>
                        <th style="width: 30%">Message</th>
                        <th style="width: 10%">Replied</th>
                        <th style="width: 15%">Sent</th>
                        <th class="actions" style="width: 10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $m)
                        <tr>
                            <td style="font-weight: 600;">{{ $m->name }}</td>
                            <td class="muted">{{ $m->email }}</td>
                            <td class="muted small">{{ Str::limit($m->message, 80) }}</td>
                            <td>
                                @if($m->replied_at)
                                    <span style="color: var(--primary); font-weight: 600;">Yes</span>
                                @else
                                    <span class="muted">No</span>
                                @endif
                            </td>
                            <td class="muted small">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.contact.show', $m) }}" style="font-weight: 600;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($messages, 'links'))
            <div style="margin-top: 1rem;">
                {{ $messages->links() }}
            </div>
        @endif
    @else
        <div class="admin-table-wrapper" style="padding: 2rem; text-align: center; color: var(--text-muted);">
            No recent contact forms.
        </div>
    @endif
@endsection
