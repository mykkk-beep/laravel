<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdminActivity extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'description',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
