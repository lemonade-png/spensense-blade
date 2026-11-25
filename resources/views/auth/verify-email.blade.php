@extends('layout')
@section('title', 'Verify email')
@section('content')
    <section class="panel narrow">
        <h1>Verify email</h1>
        <p>Open the verification link sent to your email address.</p>
        @if ($status === 'verification-link-sent') <p role="status">A new link has been sent.</p> @endif
        <form method="post" action="{{ route('verification.send') }}">@csrf <button>Resend verification email</button></form>
    </section>
@endsection
