@extends('layouts.app')

@section('title', 'Manage Program')

@section('content')
    <p><a href="{{ route('program.create') }}">Create new item</a></p>

    @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    @if($programs->isEmpty())
        <p>No program items yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Day</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programs as $p)
                    <tr>
                        <td>{{ $p->title }}</td>
                        <td>{{ $p->day_of_week }}</td>
                        <td>{{ $p->start_time }}</td>
                        <td>{{ $p->end_time }}</td>
                        <td>{{ $p->published ? 'Yes' : 'No' }}</td>
                        <td>
                            <a href="{{ route('program.edit', $p->id) }}">Edit</a>
                            <form method="POST" action="{{ route('program.destroy', $p->id) }}" style="display:inline" onsubmit="return confirm('Delete this item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

@endsection

