<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required',
            'content' => 'required',
            'image' => 'nullable|image',
        ]);

        $data = $request->all();

        // 2. Data Processing (YAHAN HAI FIX 🛠️)
        $data['slug'] = Str::slug($request->title) . '-' . time();
        $data['user_id'] = auth()->id() ?? 1;
        $data['author_name'] = $request->author_name ?? 'Frontend User';
        $data['status'] = $request->status ?? 'draft';
        $data['is_breaking'] = $request->is_breaking == '1' ? 1 : 0;

        // 🔥 MAGIC LINE: Content se tags hatao aur 150 words ka saaf summary banao
        // html_entity_decode: Ye '&nbsp;' ko hatayega
        // strip_tags: Ye <p> <b> ko hatayega
        $cleanContent = strip_tags(html_entity_decode($request->content));
        $data['excerpt'] = Str::limit($cleanContent, 150, '...'); 

        // 3. Image Upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news-images', 'public');
        }

        // 4. Save
        $news = News::create($data);

        return response()->json([
            'message' => 'News Created Successfully!',
            'data' => $news
        ], 201);
    }
}