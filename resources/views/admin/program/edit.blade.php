@extends('layouts.app')

@section('title', 'Edit Program Item')

@section('content')
    <form method="POST" action="{{ route('program.update', $program->id) }}">
        @csrf
        @method('PUT')
        @include('admin.program._form')
        <button type="submit">Save</button>
    </form>
@endsection

