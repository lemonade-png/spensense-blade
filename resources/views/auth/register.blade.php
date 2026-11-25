@extends('layout')
@section('title', 'Register')
@section('content')
    <form class="panel narrow grid" method="post" action="{{ route('register.store') }}">
        @csrf
        <h1>Create account</h1>
        <label>Name <input name="name" value="{{ old('name') }}" autocomplete="name" required></label>
        <label>Email <input name="email" type="email" value="{{ old('email') }}" autocomplete="email" required></label>
        <label>Password <input type="password" name="password" autocomplete="new-password" required></label>
        <label>Confirm password <input type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button>Create account</button>
    </form>
@endsection
