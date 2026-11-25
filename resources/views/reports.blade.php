@extends('layout')
@section('title', 'Reports')
@section('content')
    <header class="heading"><h1>Reports</h1>@include('partials.month')</header>
    <div class="pair">
        <section class="panel"><h2>Monthly income</h2><strong class="income">PKR {{ number_format((float) $totals['income'], 2) }}</strong></section>
        <section class="panel"><h2>Monthly expenses</h2><strong class="expense">PKR {{ number_format((float) $totals['expenses'], 2) }}</strong></section>
    </div>
    @include('partials.charts')
@endsection
