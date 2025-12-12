@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <section class="dashboard-section">
        <div class="admin-header">
            <h2>Manage Users</h2>
            <a href="{{ route('admin.users.create') }}" class="btn">
                + Create New User
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 30%">Name</th>
                        <th style="width: 30%">Email</th>
                        <th style="width: 10%">Admin</th>
                        <th class="actions" style="width: 30%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td style="font-weight: 600;">{{ $u->name }}</td>
                            <td class="muted">{{ $u->email }}</td>
                            <td>
                                @if($u->is_admin)
                                    <span style="color: var(--primary); font-weight: 600;">Yes</span>
                                @else
                                    <span class="muted">No</span>
                                @endif
                            </td>
                            <td class="actions">
                                <div class="admin-actions">
                                    @if(auth()->user()->id !== $u->id)
                                        <form action="{{ route('admin.users.toggleAdmin', $u) }}" method="POST" style="display:inline; margin-right: 0.5rem;">
                                            @csrf
                                            <button type="submit" style="background: transparent; border: none; color: var(--primary); font-weight: 600; cursor: pointer; padding: 0;">
                                                {{ $u->is_admin ? 'Demote' : 'Promote' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: transparent; border: none; color: #E63946; font-weight: 600; cursor: pointer; padding: 0;">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="muted small">Current User</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
