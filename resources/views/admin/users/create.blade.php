@extends('layouts.app')

@section('title', 'Create User')

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

    <form method="POST" action="{{ route('admin.users.store') }}" class="form-card">
        @csrf
        <div>
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Admin</label>
            <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
        </div>
        <button type="submit">Create</button>
    </form>
@endsection

