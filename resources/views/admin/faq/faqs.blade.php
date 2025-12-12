@extends('layouts.app')

@section('title', 'Manage FAQs')

@section('content')
    <section class="dashboard-section">
        <div class="admin-header">
            <h2>Manage FAQs</h2>
            <a href="{{ route('faq.create') }}" class="btn">
                + Create New FAQ
            </a>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 30%">Question</th>
                        <th style="width: 20%">Category</th>
                        <th>Answer</th>
                        <th class="actions" style="width: 120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                        <tr>
                            <td>
                                {{ $faq->question }}
                            </td>
                            <td class="muted">
                                {{ $faq->category->name ?? 'Uncategorized' }}
                            </td>
                            <td class="muted small">
                                {{ Str::limit(strip_tags($faq->answer), 60) }}
                            </td>
                            <td class="actions">
                                <div class="admin-actions">
                                    <a href="{{ route('faq.edit', $faq->id) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                                        Edit
                                    </a>
                                    <form action="{{ route('faq.destroy', $faq->id) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('Are you sure you want to delete this FAQ?');">
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
                                No FAQs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($faqs, 'links'))
            <div style="margin-top: 2rem;">
                {{ $faqs->links() }}
            </div>
        @endif
    </section>
@endsection
