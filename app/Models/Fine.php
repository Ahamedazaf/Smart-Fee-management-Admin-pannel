<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'total_amount',
        'details_json', 
    ];

    protected $casts = [
        'details_json' => 'array', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
