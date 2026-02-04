<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'bio',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'social_facebook',
        'social_twitter',
        'social_instagram',
        'social_linkedin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}