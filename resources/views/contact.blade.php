@extends('layouts.app')

@section('title', 'Contact')

@section('content')
    <div class="container">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form action="{{ route('contact.send') }}" method="POST">
            @csrf

            <div>
                <label for="name">Naam</label>
                <input id="name" name="name" value="{{ old('name') }}">
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="email">E-mail</label>
                <input id="email" name="email" value="{{ old('email') }}">
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div>
                <label for="message">Bericht</label>
                <textarea id="message" name="message">{{ old('message') }}</textarea>
                @error('message') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <button type="submit">Verstuur</button>
        </form>
    </div>
@endsection

