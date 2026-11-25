@extends('layout')
@section('title', 'Profile')
@section('content')
    <h1>Profile</h1>
    <nav class="actions" aria-label="Settings"><a href="{{ route('security.edit') }}">Change password</a> <a href="{{ route('appearance.edit') }}">Appearance</a></nav>
    <form class="panel narrow grid" method="post" action="{{ route('profile.update') }}">
        @csrf @method('PATCH')
        <h2>Account details</h2>
        <label>Name <input name="name" value="{{ old('name', auth()->user()->name) }}" required></label>
        <label>Email <input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required></label>
        <button>Save profile</button>
    </form>
    @if ($mustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
        <p><a href="{{ route('verification.notice') }}">Verify your email address</a></p>
    @else
        <details class="panel narrow">
            <summary>Delete account</summary>
            <form class="grid" method="post" action="{{ route('profile.destroy') }}" data-confirm="Delete this account and all of its records?">
                @csrf @method('DELETE')
                <label>Current password <input type="password" name="password" autocomplete="current-password" required></label>
                <button>Delete account</button>
            </form>
        </details>
    @endif
@endsection
