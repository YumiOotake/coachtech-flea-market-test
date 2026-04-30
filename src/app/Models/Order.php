<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'postal_code',
        'address',
        'building',
        'payment_method',
    ];

    protected $casts = [
        'item_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            0 => 'コンビニ支払い',
            1 => 'カード支払い',
            default => 'コンビニ支払い'
        };
    }
}
