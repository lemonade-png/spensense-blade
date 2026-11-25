@extends('layout')
@section('title', 'Overview')
@section('content')
    <header class="heading page-heading">
        <div>
            <h1>Overview</h1>
            <p>Your recorded income, expenses, and spending over time.</p>
        </div>
        <a class="button" href="{{ route('transactions.create') }}"><span aria-hidden="true">+</span> Add transaction</a>
    </header>
    <div class="metrics">
        <section class="panel metric balance-card">
            <h2>Recorded balance <span class="metric-icon" aria-hidden="true">≋</span></h2>
            <strong><span class="currency">PKR</span> {{ number_format((float) $totals['balance'], 2) }}</strong>
            <small>Income minus expenses</small>
        </section>
        <section class="panel metric">
            <h2>Total income <span class="metric-icon income" aria-hidden="true">↗</span></h2>
            <strong class="income"><span class="currency">PKR</span> {{ number_format((float) $totals['income'], 2) }}</strong>
        </section>
        <section class="panel metric">
            <h2>Total expenses <span class="metric-icon expense" aria-hidden="true">↘</span></h2>
            <strong class="expense"><span class="currency">PKR</span> {{ number_format((float) $totals['expenses'], 2) }}</strong>
        </section>
        <section class="panel metric">
            <h2>Transactions <span class="metric-icon" aria-hidden="true">≡</span></h2>
            <strong>{{ number_format($totals['count']) }}</strong>
        </section>
    </div>
    @include('partials.charts')
    <section class="panel transaction-panel">
        <header class="heading panel-heading"><h2>Recent transactions</h2><a class="button secondary compact" href="{{ route('transactions.index') }}">View all</a></header>
        @include('partials.transactions', ['rows' => $recent])
    </section>
@endsection
