@extends('layouts.app')

@section('title', 'Manage Resources')

@section('content')
    <section class="dashboard-section">
        <div class="admin-header">
            <h2>Current Resources</h2>
            <a href="{{ route('resources.create') }}" class="btn">
                + Create New Resource
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
                        <th style="width: 20%">Category</th>
                        <th>Snippet</th>
                        <th class="actions" style="width: 120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resources as $resource)
                        <tr>
                            <td>
                                {{ $resource->title }}
                            </td>
                            <td class="muted">
                                {{ $resource->category->name ?? 'Uncategorized' }}
                            </td>
                            <td class="muted small">
                                {{ Str::limit(strip_tags($resource->content), 60) }}
                            </td>
                            <td class="actions">
                                <div class="admin-actions">
                                    <a href="{{ route('resources.edit', $resource->id) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                        Edit
                                    </a>
                                    <form action="{{ route('resources.destroy', $resource->id) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Are you sure you want to delete this resource?');">
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
                            <td colspan="4" class="muted" style="text-align: center; padding: 2rem;">
                                No resources found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($resources, 'links'))
            <div style="margin-top: 2rem;">
                {{ $resources->links() }}
            </div>
        @endif
    </section>
@endsection
