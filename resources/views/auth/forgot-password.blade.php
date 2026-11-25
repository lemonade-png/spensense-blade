@extends('layout')
@section('title', 'Reset password')
@section('content')
    <form class="panel narrow grid" method="post" action="{{ route('password.email') }}">
        @csrf
        <h1>Reset password</h1>
        @if ($status) <p role="status">{{ $status }}</p> @endif
        <label>Email <input name="email" type="email" value="{{ old('email') }}" required></label>
        <button>Send reset link</button>
    </form>
@endsection
