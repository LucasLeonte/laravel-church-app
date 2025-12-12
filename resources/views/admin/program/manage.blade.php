@extends('layouts.app')

@section('title', 'Manage Program')

@section('content')
    <section class="dashboard-section">
        <div class="admin-header">
            <h2>Manage Program</h2>
            <a href="{{ route('program.create') }}" class="btn">
                + Create New Item
            </a>
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
                        <th style="width: 25%">Title</th>
                        <th style="width: 15%">Day</th>
                        <th style="width: 15%">Start</th>
                        <th style="width: 15%">End</th>
                        <th style="width: 10%">Published</th>
                        <th class="actions" style="width: 20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $p)
                        <tr>
                            <td style="font-weight: 600;">{{ $p->title }}</td>
                            <td class="muted">{{ $p->day_of_week }}</td>
                            <td class="muted small">{{ $p->start_time }}</td>
                            <td class="muted small">{{ $p->end_time }}</td>
                            <td>
                                @if($p->published)
                                    <span style="color: var(--primary); font-weight: 600;">Yes</span>
                                @else
                                    <span class="muted">No</span>
                                @endif
                            </td>
                            <td class="actions">
                                <div class="admin-actions">
                                    <a href="{{ route('program.edit', $p->id) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('program.destroy', $p->id) }}" style="display:inline; margin:0;" onsubmit="return confirm('Delete this item?')">
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
                                No program items yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

