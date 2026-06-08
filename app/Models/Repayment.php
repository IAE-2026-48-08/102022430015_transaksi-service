<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Repayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'loan_amount',
        'repayment_amount',
        'installment_number',
        'status',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'loan_amount' => 'float',
        'repayment_amount' => 'float',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];
}
