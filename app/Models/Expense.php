<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'title',
        'reason',
        'amount',
        'spent_at',
       
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'spent_at' => 'date',
    ];
}
