<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_paid_amount', 'emi', 'week_day', 'remaining_period'];
}
