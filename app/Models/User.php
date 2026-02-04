<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCreator()
    {
        return $this->role === 'creator';
    }

    // Mere Subscribers (Log jinhone mujhe subscribe kiya)
    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'author_id', 'subscriber_id');
    }

    // Main jinko Subscribe kiya hu (Following)
    public function subscriptions()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'subscriber_id', 'author_id');
    }
}