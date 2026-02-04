<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    // 1. NEWS LIST (Category & Search Support)
    public function index(Request $request)
    {
        $query = News::with(['user', 'category']) // 'user.profile' hata diya agar profile relation na ho
            ->where('status', 'published');

        // 👇 YEH HAI CATEGORY FILTER (India, World, Tech, etc.)
        if ($request->has('category') && $request->category != '') {
            $slug = $request->category;
            $query->whereHas('category', function($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        // Search Filter
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        // Latest News + Breaking News upar dikhegi
        $news = $query->orderBy('is_breaking', 'desc') // Breaking news pehle
                      ->latest('published_at') // Phir naye posts
                      ->paginate(12);

        return response()->json($news);
    }

    // 2. SINGLE NEWS DETAILS
    public function show($slug)
    {
        $news = News::with(['user', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        // View Count Badhao (Agar model me function hai toh)
        if (method_exists($news, 'incrementViews')) {
            $news->incrementViews();
        }

        return response()->json($news);
    }

    // 3. CREATE NEWS (API se)
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048', // 'featured_image' ko 'image' kiya
            'status' => 'in:draft,published,pending',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($request->title) . '-' . time();

        // Image Upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news-images', 'public');
        }

        // Creators can only save as pending/draft (Security)
        if (auth()->check() && auth()->user()->method_exists('isCreator') && auth()->user()->isCreator() && $request->status === 'published') {
            $data['status'] = 'pending';
        }

        $news = News::create($data);

        return response()->json($news->load(['user', 'category']), 201);
    }

    // 4. UPDATE NEWS
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        // Check permission
        if (auth()->check() && !auth()->user()->isAdmin() && $news->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'category_id' => 'exists:categories,id',
            'title' => 'string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'string',
            'image' => 'nullable|image|max:2048',
            'status' => 'in:draft,published,pending',
        ]);

        $data = $request->all();

        // Image Update logic
        if ($request->hasFile('image')) {
            // Purani image delete karo agar hai toh
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('news-images', 'public');
        }

        if ($request->title && $request->title !== $news->title) {
            $data['slug'] = Str::slug($request->title) . '-' . time();
        }

        $news->update($data);

        return response()->json($news->load(['user', 'category']));
    }

    // 5. DELETE NEWS
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        if (auth()->check() && !auth()->user()->isAdmin() && $news->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Image bhi delete karo
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return response()->json(['message' => 'News deleted successfully']);
    }

    // 6. MY NEWS (User Dashboard ke liye)
    public function myNews(Request $request)
    {
        $news = News::with(['category'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return response()->json($news);
    }

    // 7. APPROVE (Admin ke liye)
    public function approve($id)
    {
        $news = News::findOrFail($id);
        $news->update(['status' => 'published', 'published_at' => now()]);
        return response()->json($news);
    }
}