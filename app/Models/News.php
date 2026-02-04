<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'image',        // ✅ Ab ye Multiple Images store karega
        'author_name',  // ✅ Isey bhi add kiya (Migration mein tha)
        'is_breaking',
        'gallery',
        'status',
        'views',
        'published_at',
    ];

    protected $casts = [
        'image' => 'array',      // 🔥 MAIN FIX: Laravel ko bataya ki ye JSON List hai
        'gallery' => 'array',
        'published_at' => 'datetime',
        'is_breaking' => 'boolean',
    ];

    // User relationship (author of the news)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Category relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Comments relationship
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    // Likes relationship
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Increment views method
    public function incrementViews()
    {
        $this->increment('views');
    }
}