<?php

namespace App\Models;

use App\User;
use Eloquent;

class PaymentRecord extends Eloquent
{
    protected $fillable = [
        'student_id',
        'payment_id',
        'additional_amount_paid',
        'amt_paid',
        'today_paid',
        'year',
        'paid',
        'amt_due',  
        'balance',
        'ref_no',
        'paid_months', 
    ];

    protected $casts = [
        'paid_months' => 'array', 
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function receipt()
    {
        return $this->hasMany(Receipt::class, 'pr_id');
    }
}