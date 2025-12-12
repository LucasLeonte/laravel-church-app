@extends('layouts.app')

@section('title', 'Manage FAQ Categories')

@section('content')
    <section class="dashboard-section">
        <div class="admin-header">
            <h2>Current Categories</h2>
            <a href="{{ route('faq.categories.create') }}" class="btn">
                + Create New Category
            </a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70%">Category Name</th>
                        <th class="actions" style="width: 30%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                {{ $category->name }}
                            </td>
                            <td class="actions">
                                <div class="admin-actions">
                                    <a href="{{ route('faq.categories.edit', $category->id) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                        Edit
                                    </a>
                                    <form action="{{ route('faq.categories.destroy', $category->id) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Are you sure you want to delete this category?');">
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
                            <td colspan="2" class="muted" style="text-align: center; padding: 2rem;">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

