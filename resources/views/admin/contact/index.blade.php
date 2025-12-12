@extends('layouts.app')

@section('title', 'Contact messages')

@section('content')
    <section class="dashboard-section">
        <div class="admin-header">
            <h2>Contact Messages</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 20%">Name</th>
                        <th style="width: 25%">Email</th>
                        <th style="width: 30%">Message</th>
                        <th style="width: 10%">Replied</th>
                        <th style="width: 15%">Submitted</th>
                        <th class="actions" style="width: 120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $m)
                        <tr>
                            <td style="font-weight: 600;">{{ $m->name }}</td>
                            <td class="muted">{{ $m->email }}</td>
                            <td class="muted small">{{ Str::limit($m->message, 60) }}</td>
                            <td>
                                @if($m->replied_at)
                                    <span style="color: var(--primary); font-weight: 600;">Yes</span>
                                @else
                                    <span class="muted">No</span>
                                @endif
                            </td>
                            <td class="muted small">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                            <td class="actions">
                                <div class="admin-actions">
                                    <a href="{{ route('admin.contact.show', $m) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                        View
                                    </a>
                                    <form action="{{ route('admin.contact.destroy', $m) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Delete this message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: transparent; border: none; color: #E63946; font-weight: 600; cursor: pointer; padding: 0;">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="muted" style="text-align: center; padding: 2rem;">
                                No messages found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($messages, 'links'))
            <div style="margin-top: 2rem;">
                {{ $messages->links() }}
            </div>
        @endif
    </section>
@endsection
