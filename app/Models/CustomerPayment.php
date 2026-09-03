<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    protected $fillable = ['payment_number', 'sale_id', 'payment_date', 'amount', 'payment_method', 'reference_number', 'notes', 'created_by'];
    protected $casts = ['payment_date' => 'date'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
