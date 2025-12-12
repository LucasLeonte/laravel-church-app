@extends('layouts.app')

@section('title', 'Edit Users')

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="form-card">
        @csrf
        @method('PUT')
        <div>
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div>
            <label>Password (leave blank to keep)</label>
            <input type="password" name="password">
        </div>
        <div>
            <label>Admin</label>
            <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
        </div>
        <button type="submit">Save</button>
    </form>
@endsection

