@extends('layout')
@section('title', 'Budgets')
@section('content')
    <header class="heading"><h1>Budgets</h1>@include('partials.month')</header>
    <div class="columns">
        <div class="grid">
            @forelse ($budgets as $budget)
                <section class="panel">
                    <h2>{{ $budget['category']['name'] }}</h2>
                    <p>{{ $budget['percentage'] }}% used</p>
                    <progress max="100" value="{{ min(100, $budget['percentage']) }}" aria-label="{{ $budget['category']['name'] }} budget used"></progress>
                    <p>Limit: PKR {{ number_format((float) $budget['amount'], 2) }} · Spent: PKR {{ number_format((float) $budget['spent'], 2) }} · Remaining: PKR {{ number_format((float) $budget['remaining'], 2) }}</p>
                    <details>
                        <summary>Edit budget</summary>
                        <form method="post" action="{{ route('budgets.update', $budget['id']) }}" class="grid">
                            @csrf @method('PUT')
                            @include('partials.budget-fields')
                            <button>Save changes</button>
                        </form>
                    </details>
                    <form method="post" action="{{ route('budgets.destroy', $budget['id']) }}" data-confirm="Delete this budget?">
                        @csrf @method('DELETE') <button class="secondary">Delete</button>
                    </form>
                </section>
            @empty
                <p class="panel">No budgets for this month.</p>
            @endforelse
        </div>
        <form method="post" action="{{ route('budgets.store') }}" class="panel grid">
            @csrf
            <h2>New budget</h2>
            @include('partials.budget-fields', ['budget' => null])
            <button @disabled($categories->isEmpty())>Add budget</button>
        </form>
    </div>
@endsection
