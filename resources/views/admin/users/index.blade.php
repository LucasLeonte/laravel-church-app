@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p><a href="{{ route('admin.users.create') }}">Create new user</a></p>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->is_admin ? 'Yes' : 'No' }}</td>
                <td>
                    @if(auth()->user()->id !== $u->id)
                        <form action="{{ route('admin.users.toggleAdmin', $u) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit">{{ $u->is_admin ? 'Demote' : 'Promote' }}</button>
                        </form>

                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    @else
                        <em>—</em>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
