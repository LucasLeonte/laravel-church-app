@extends('layouts.app')

@section('title', 'Create Program Item')

@section('content')
    <form action="{{ route('program.store') }}" method="POST" class="form-card">
        @csrf
        @include('admin.program._form')
        <button type="submit">Create</button>
    </form>
@endsection
