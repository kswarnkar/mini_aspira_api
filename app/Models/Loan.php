<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'yearly_term', 'percentage', 'weekly_term', 'emi', 'status'];

    public static function emi_calculator($p, $r, $t)
    {
        // one week interest
        $r = $r / (52 * 100);
        // one week period
        $t = $t * 52;
        $emi = ($p * $r * pow(1 + $r, $t)) / (pow(1 + $r, $t) - 1);
        return $emi;
    }
}
