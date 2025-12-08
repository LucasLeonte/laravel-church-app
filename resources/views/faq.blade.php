@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
     @can('admin')
        <div>
            <a href="{{ route('faq.categories.create') }}">Create category</a>
            <a href="{{ route('faq.categories.index') }}">Manage categories</a>
            <a href="{{ route('faq.faqs.create') }}">Create FAQ</a>
            <a href="{{ route('faq.faqs.index') }}">Manage FAQs</a>
        </div>
    @endcan

    <!-- Search form -->
    <form method="GET" action="{{ route('faq.index') }}" style="margin:1rem 0">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search FAQs by question, answer or category" />
        <button type="submit">Search</button>
        @if(request('q'))
            <a href="{{ route('faq.index') }}">Clear</a>
        @endif
    </form>

    @foreach($categories as $category)
        <section>
            <h2>{{ $category->name }}</h2>

            @if($category->description)
                <p>{{ $category->description }}</p>
            @endif

            <ul>
                @foreach($category->faqs as $faq)
                    <li>
                        <strong>{{ $faq->question }}</strong>
                        <div>{{ $faq->answer }}</div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endforeach
@endsection
