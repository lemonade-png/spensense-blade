<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property-read int|float|string|null $total Selected by the report aggregate queries. */
class Transaction extends Model
{
    protected $fillable = ['category_id', 'title', 'type', 'amount', 'transaction_date', 'notes'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'transaction_date' => 'date:Y-m-d'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
