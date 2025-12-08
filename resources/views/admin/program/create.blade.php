@extends('layouts.app')

@section('title', 'Create Program Item')

@section('content')
    <form method="POST" action="{{ route('program.store') }}">
        @csrf
        @include('admin.program._form')
        <button type="submit">Create</button>
    </form>
@endsection

