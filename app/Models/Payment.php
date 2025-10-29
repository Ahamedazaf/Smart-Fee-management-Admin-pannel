<?php

namespace App\Models;

use Eloquent;

class Payment extends Eloquent
{
    protected $fillable = ['title', 'my_class_id', 'ref_no', 'additional_items', 'additional_amount', 'amount', 'total_amount', 'description', 'year'];

    public function my_class()
    {
        return $this->belongsTo(MyClass::class);
    }
}
