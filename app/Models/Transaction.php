<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'amount',
        'note',
        'image',
        
        'user_id',
        'customer_id',
        'number',
        'pay_amount',
        'cash_change',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    // scope untuk mengambil data is_expense dari category
    public function scopeExpenses($query)
    {
        return $query->whereHas('category', function ($query) {
            $query->where('is_expense', true);
        });
    }

    public function scopeIncomes($query)
    {
        return $query->whereHas('category', function ($query) {
            $query->where('is_expense', false);
        });
    }
}
