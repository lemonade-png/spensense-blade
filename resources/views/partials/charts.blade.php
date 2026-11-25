<div class="chart-grid">
    <section class="panel chart-panel">
        <header class="heading panel-heading">
            <div><h2>Cash flow</h2><p>Six months ending {{ $period }} · PKR</p></div>
            <div class="legend"><span class="income">Income</span><span class="expense">Expenses</span></div>
        </header>
        @php($maximum = max(1, ...array_column($history, 'income'), ...array_column($history, 'expenses')))
        <div class="cash-chart" aria-hidden="true">
            @foreach ($history as $month)
                <div class="cash-month">
                    <div class="cash-bars">
                        <div class="bar income" style="--value: {{ $month['income'] / $maximum * 100 }}%" title="Income: PKR {{ number_format($month['income'], 2) }}"></div>
                        <div class="bar expense" style="--value: {{ $month['expenses'] / $maximum * 100 }}%" title="Expenses: PKR {{ number_format($month['expenses'], 2) }}"></div>
                    </div>
                    <span>{{ substr($month['period'], 0, 3) }}</span>
                </div>
            @endforeach
        </div>
        <details class="chart-details">
            <summary>View monthly totals</summary>
            <div class="table-wrap">
                <table>
                    <thead><tr><th scope="col">Month</th><th scope="col">Income</th><th scope="col">Expenses</th></tr></thead>
                    <tbody>
                        @foreach ($history as $month)
                            <tr><td>{{ $month['period'] }}</td><td class="income">{{ number_format($month['income'], 2) }}</td><td class="expense">{{ number_format($month['expenses'], 2) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    </section>
    <section class="panel chart-panel">
        <header class="panel-heading"><h2>Spending by category</h2><p>{{ $period }}</p></header>
        @php($maximumCategory = max(1, $breakdown->max('amount')))
        <div class="category-chart">
            @forelse ($breakdown as $category)
                <div class="category-bar">
                    <div class="heading"><strong>{{ $category['name'] }}</strong><span>PKR {{ number_format($category['amount'], 2) }}</span></div>
                    <div class="bar-track" aria-hidden="true"><span style="--value: {{ $category['amount'] / $maximumCategory * 100 }}%"></span></div>
                </div>
            @empty
                <p class="empty-state">No expenses recorded for this month.</p>
            @endforelse
        </div>
    </section>
</div>
