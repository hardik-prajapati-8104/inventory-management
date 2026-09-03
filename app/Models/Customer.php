<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_code', 'customer_name', 'company_name', 'mobile', 'whatsapp', 'email',
        'address', 'city', 'country', 'tax_number', 'opening_balance', 'credit_limit',
        'payment_terms', 'discount_percentage', 'notes', 'status',
    ];
    protected $casts = ['status' => 'boolean', 'opening_balance' => 'decimal:2', 'credit_limit' => 'decimal:2'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return (float) $this->opening_balance + (float) $this->sales()->sum('due_amount');
    }
}
