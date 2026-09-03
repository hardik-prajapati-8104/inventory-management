<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['expense_number', 'category', 'expense_date', 'amount', 'payment_method', 'description', 'attachment', 'created_by'];
    protected $casts = ['expense_date' => 'date'];

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
