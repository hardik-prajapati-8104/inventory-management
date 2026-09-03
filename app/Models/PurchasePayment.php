<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = ['payment_number', 'purchase_id', 'payment_date', 'amount', 'payment_method', 'reference_number', 'notes', 'created_by'];
    protected $casts = ['payment_date' => 'date'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
