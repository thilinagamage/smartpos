<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'credit_limit', 'opening_balance', 'status'];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'credit_limit' => 'decimal:2',
            'opening_balance' => 'decimal:2',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->sales()->where('status', 'completed')->sum('total_amount');
    }

    public function getTotalPaidAttribute()
    {
        return $this->sales()->where('status', 'completed')->sum('paid_amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_purchases - $this->total_paid;
    }
}
