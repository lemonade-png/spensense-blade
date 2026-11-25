<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'type' => ['nullable', Rule::in(['income', 'expense'])],
            'category' => ['nullable', 'integer', 'min:1'],
        ]);
        $user = $request->user();

        $transactions = $user->transactions()->with('category:id,name')
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where('title', 'like', '%'.$filters['search'].'%');
            })
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['category'] ?? null, fn ($query, $id) => $query->where('category_id', $id))
            ->orderByDesc('transaction_date')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('transactions/index', [
            'transactions' => $transactions,
            'filters' => $filters,
            'categories' => $this->categories($user),
        ]);
    }

    public function create(Request $request): View
    {
        return view('transactions/form', [
            'categories' => $this->categories($request->user()),
            'today' => today()->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->transactions()->create($this->validated($request));

        return to_route('transactions.index');
    }

    public function edit(Request $request, string $transaction): View
    {
        return view('transactions/form', [
            'transaction' => $request->user()->transactions()->findOrFail($transaction),
            'categories' => $this->categories($request->user()),
            'today' => today()->toDateString(),
        ]);
    }

    public function update(Request $request, string $transaction): RedirectResponse
    {
        $record = $request->user()->transactions()->findOrFail($transaction);
        $record->update($this->validated($request));

        return to_route('transactions.index');
    }

    public function destroy(Request $request, string $transaction): RedirectResponse
    {
        $request->user()->transactions()->findOrFail($transaction)->delete();

        return to_route('transactions.index');
    }

    /** @return Collection<int, Category> */
    private function categories(User $user): Collection
    {
        return $user->categories()->orderBy('name')->get(['id', 'name', 'type']);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $type = $request->validate(['type' => ['required', Rule::in(['income', 'expense'])]])['type'];

        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999.99'],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')
                ->where('user_id', $request->user()->id)->where('type', $type)],
            'transaction_date' => ['required', 'date_format:Y-m-d'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
