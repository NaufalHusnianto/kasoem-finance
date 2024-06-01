<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    protected static function booted()
    {
        static::created(function ($item) {
            $item->decreaseProductStock();
        });
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function decreaseProductStock()
    {
        // Log::info('Decreasing stock for product', ['product_id' => $this->product_id, 'quantity' => $this->quantity]);
        $product = Product::find($this->product_id);
        if ($product) {
            $product->stock -= $this->quantity;
            $product->save();
        }
    }
}
