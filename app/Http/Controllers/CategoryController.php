<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('categories', [
            'categories' => $request->user()->categories()->withCount(['transactions', 'budgets'])
                ->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->categories()->create($this->validated($request));

        return to_route('categories.index');
    }

    public function update(Request $request, string $category): RedirectResponse
    {
        DB::transaction(function () use ($request, $category): void {
            $record = $request->user()->categories()->lockForUpdate()->findOrFail($category);
            $data = $this->validated($request, $record);
            if ($record->type !== $data['type'] && $this->inUse($record)) {
                throw ValidationException::withMessages(['type' => 'A category in use must keep its current type.']);
            }

            $record->update($data);
        });

        return to_route('categories.index');
    }

    public function destroy(Request $request, string $category): RedirectResponse
    {
        DB::transaction(function () use ($request, $category): void {
            $record = $request->user()->categories()->lockForUpdate()->findOrFail($category);
            if ($this->inUse($record)) {
                throw ValidationException::withMessages(['category' => 'Move or remove its transactions and budgets before deleting this category.']);
            }

            $record->delete();
        });

        return to_route('categories.index');
    }

    private function inUse(Category $category): bool
    {
        return $category->transactions()->exists() || $category->budgets()->exists();
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Category $category = null): array
    {
        $type = $request->validate(['type' => ['required', Rule::in(['income', 'expense'])]])['type'];

        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('categories')
                ->where('user_id', $request->user()->id)->where('type', $type)->ignore($category?->id)],
            'type' => ['required', Rule::in(['income', 'expense'])],
        ]);
    }
}
