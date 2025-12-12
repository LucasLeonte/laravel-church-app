@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
    @can('admin')
        <section class="dashboard-section" style="background: #f8faf8; padding: 1rem 1.5rem; border-radius: var(--radius-md); border: 1px dashed var(--border-color); display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <span style="font-weight: 700; color: var(--text-muted); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em;">Admin Controls</span>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <a href="{{ route('faq.create') }}" class="btn" style="padding: 0.4rem 1rem; font-size: 0.9rem;">+ Create FAQ</a>
                <a href="{{ route('faq.manage') }}" style="font-size: 0.9rem; color: var(--primary-dark); font-weight: 500; display: inline-flex; align-items: center;">Manage FAQs</a>
                <span style="border-left: 1px solid var(--border-color); height: 1.5rem; margin: 0 0.5rem;"></span>
                <a href="{{ route('faq.categories.create') }}" style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500; display: inline-flex; align-items: center;">+ Create Category</a>
                <a href="{{ route('faq.categories.index') }}" style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500; display: inline-flex; align-items: center;">Manage Categories</a>
            </div>
        </section>
    @endcan

    <!-- Search form -->
    <section class="dashboard-section">
        <div style="background: var(--bg-surface); padding: 2rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem; border-bottom: none;">Find Questions</h2>
            <form method="GET" action="{{ route('faq.index') }}">
                <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; margin-bottom: 1rem;">
                    <div style="flex-grow: 1;">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search FAQs by question, answer or category" style="width: 100%; padding: 0.8rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 1rem;" />
                    </div>
                    <div>
                        <button type="submit" class="btn" style="padding: 0.8rem 2rem;">Search</button>
                    </div>
                </div>
                @if(request('q'))
                    <div style="text-align: right;">
                        <a href="{{ route('faq.index') }}" style="font-size: 0.9rem; color: var(--text-muted);">Clear Search</a>
                    </div>
                @endif
            </form>
        </div>
    </section>

    @foreach($categories as $category)
        <section class="dashboard-section section-faq">
            <h2 style="margin-bottom: 1.5rem;">{{ $category->name }}</h2>

            @if($category->description)
                <p style="margin-bottom: 1.5rem; color: var(--text-muted);">{{ $category->description }}</p>
            @endif

            <div class="faq-accordion">
                @foreach($category->faqs as $faq)
                    <details>
                        <summary>{{ $faq->question }}</summary>
                        <div class="faq-answer">
                            {{ $faq->answer }}
                        </div>
                    </details>
                @endforeach
            </div>
        </section>
    @endforeach
@endsection
