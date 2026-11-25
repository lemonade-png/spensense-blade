<div class="table-wrap">
    <table>
        <thead><tr><th scope="col">Transaction</th><th scope="col">Category</th><th scope="col">Date</th><th scope="col">Amount</th>@if ($editable ?? false)<th scope="col">Actions</th>@endif</tr></thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td><strong>{{ $row->title }}</strong>@if ($row->notes)<small>{{ $row->notes }}</small>@endif</td>
                    <td>{{ $row->category->name }}</td>
                    <td>{{ $row->transaction_date->toDateString() }}</td>
                    <td class="{{ $row->type }}">{{ $row->type === 'income' ? '+' : '−' }} PKR {{ number_format((float) $row->amount, 2) }}</td>
                    @if ($editable ?? false)
                        <td class="actions">
                            <a class="button secondary" href="{{ route('transactions.edit', $row) }}">Edit</a>
                            <form method="post" action="{{ route('transactions.destroy', $row) }}" data-confirm="Delete this transaction?">
                                @csrf @method('DELETE') <button class="secondary">Delete</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td class="empty-state" colspan="{{ ($editable ?? false) ? 5 : 4 }}">No transactions to display.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
