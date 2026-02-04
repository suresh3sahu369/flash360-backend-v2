<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ✅ DB Facade Zaroori hai

class InteractionController extends Controller
{
    // 1. NEWS DETAILS LAO (With Author, Likes, Comments & Subscription Status)
    public function showNewsWithDetails(Request $request, $slug)
    {
        $user = $request->user('sanctum'); // Check karo user login hai ya nahi

        $news = News::where('slug', $slug)
            ->with(['user', 'category']) // Author aur Category sath lao
            ->withCount(['likes', 'comments']) // Total Likes/Comments gino
            ->firstOrFail();

        // 👇 1. Author ke Total Subscribers gino
        $subscribersCount = DB::table('subscriptions')
            ->where('author_id', $news->user_id)
            ->count();

        // Check: Kya current user ne like/subscribe kiya hai?
        $isLiked = false;
        $isSubscribed = false;

        if ($user) {
            // Like Check
            $isLiked = Like::where('user_id', $user->id)
                           ->where('news_id', $news->id)
                           ->exists();

            // 👇 2. Subscription Check (Kya maine is author ko subscribe kiya hai?)
            $isSubscribed = DB::table('subscriptions')
                ->where('subscriber_id', $user->id)
                ->where('author_id', $news->user_id)
                ->exists();
        }

        return response()->json([
            'news' => $news,
            'is_liked' => $isLiked,
            'likes_count' => $news->likes_count,
            'is_subscribed' => $isSubscribed,       // ✅ Frontend ko bhejo
            'subscribers_count' => $subscribersCount // ✅ Frontend ko bhejo
        ]);
    }

    // 2. LIKE / UNLIKE KARO
    public function toggleLike(Request $request, $id)
    {
        $user = $request->user();
        $news = News::findOrFail($id);

        $existingLike = Like::where('user_id', $user->id)->where('news_id', $news->id)->first();

        if ($existingLike) {
            $existingLike->delete(); // Already liked tha, toh unlike karo
            $liked = false;
        } else {
            Like::create(['user_id' => $user->id, 'news_id' => $news->id]); // Like karo
            $liked = true;
        }

        // Naya count bhejo
        $count = Like::where('news_id', $news->id)->count();

        return response()->json(['liked' => $liked, 'count' => $count]);
    }

    // 3. COMMENT POST KARO
    public function storeComment(Request $request, $id)
    {
        $request->validate(['content' => 'required|string']);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'news_id' => $id,
            'content' => $request->content
        ]);

        // Comment ke sath User ka naam/photo turant bhejo taaki frontend par dikhe
        $comment->load('user');

        return response()->json(['message' => 'Comment Added', 'comment' => $comment]);
    }

    // 4. COMMENTS LIST LAO
    public function getComments($id)
    {
        $comments = Comment::where('news_id', $id)
            ->with('user') // User details ke sath
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    // 5. ADMIN: SAARRE COMMENTS DEKHO
    public function getAllComments()
    {
        // Saare comments lao, newest pehle
        $comments = Comment::with(['user', 'news:id,title'])->orderBy('created_at', 'desc')->get();
        return response()->json($comments);
    }

    // 6. ADMIN: COMMENT DELETE KARO
    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    }

    // 7. SUBSCRIBE / UNSUBSCRIBE
    public function toggleSubscribe(Request $request, $authorId)
    {
        $subscriberId = $request->user()->id;

        if ($subscriberId == $authorId) {
            return response()->json(['message' => 'You cannot subscribe to yourself!'], 400);
        }

        // Check karo pehle se subscribe hai ya nahi
        $exists = DB::table('subscriptions')
            ->where('subscriber_id', $subscriberId)
            ->where('author_id', $authorId)
            ->exists();

        if ($exists) {
            // Unsubscribe
            DB::table('subscriptions')
                ->where('subscriber_id', $subscriberId)
                ->where('author_id', $authorId)
                ->delete();
            $subscribed = false;
        } else {
            // Subscribe
            DB::table('subscriptions')->insert([
                'subscriber_id' => $subscriberId,
                'author_id' => $authorId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $subscribed = true;
        }

        // Total Subscribers gino
        $count = DB::table('subscriptions')
            ->where('author_id', $authorId)
            ->count();

        return response()->json(['subscribed' => $subscribed, 'count' => $count]);
    }
}