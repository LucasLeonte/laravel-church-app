@extends('layouts.app')

@section('title', $faq->exists ? 'Edit FAQ' : 'Create FAQ')

@section('content')
    <form method="POST" action="{{ $faq->exists ? route('faq.faqs.update', $faq->id) : route('faq.faqs.store') }}">
        @csrf
        @if($faq->exists)
            @method('PUT')
        @endif
        <div>
            <label>Category</label>
            <select name="faq_category_id" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('faq_category_id', $faq->faq_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Question</label>
            <input name="question" value="{{ old('question', $faq->question) }}" required>
        </div>
        <div>
            <label>Answer</label>
            <textarea name="answer" required>{{ old('answer', $faq->answer) }}</textarea>
        </div>
        <button type="submit">Save</button>
    </form>
@endsection
