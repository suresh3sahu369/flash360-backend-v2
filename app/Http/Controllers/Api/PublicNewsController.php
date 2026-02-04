<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class PublicNewsController extends Controller
{
    /**
     * List published news for frontend
     */
    public function index(Request $request)
    {
        $query = News::with('category')
            ->where('status', 'published')
            ->latest();

        // optional category filter
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        return response()->json(
            $query->paginate(12)
        );
    }

    /**
     * Single news by slug
     */
    public function show(string $slug)
    {
        $news = News::with('category')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return response()->json($news);
    }
}
