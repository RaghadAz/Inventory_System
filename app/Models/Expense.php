<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['person_name', 'type', 'amount', 'reason', 'is_paid', 'amount',
    'spent_at'];
    protected $casts = [
        'amount' => 'decimal:2',
        'spent_at' => 'date',
    ];
}
