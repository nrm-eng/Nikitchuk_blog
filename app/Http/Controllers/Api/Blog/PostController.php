<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Parsedown;

class PostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with(['user', 'category'])->get();

        return response()->json([
            'data' => $posts,
            'status' => 'success'
        ]);
    }

    public function show($id)
    {
        $post = BlogPost::with(['user', 'category'])->find($id);

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        return response()->json($post);
    }

    public function store(Request $request)
    {
        // Валідація
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content_raw' => 'required|string',
            'slug'        => 'sometimes|string|max:255|unique:blog_posts,slug',
            'category_id' => 'sometimes|exists:blog_categories,id',
            'user_id'     => 'required|exists:users,id',
        ]);

        // Генеруємо slug, якщо не передано
        if (empty($validated['slug'])) {
            // У Laravel є зручний фасад Str
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Дублюємо raw в html
        $validated['content_html'] = $validated['content_raw'];
        $validated['published_at'] = now();
        // Створюємо пост
        $post = BlogPost::create($validated);

        return response()->json([
            'message' => 'Post created successfully',
            'data'    => $post->load(['user', 'category']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'content_raw' => 'sometimes|string',
            'slug' => 'sometimes|string|max:255|unique:blog_posts,slug,' . $id,
            'category_id' => 'sometimes|exists:blog_categories,id',
        ]);

        if (array_key_exists('content_raw', $validated)) {
            $validated['content_html'] = $validated['content_raw'];
        }

        $post->update($validated);
        $post->touch();  // <-- примусово оновить тільки updated_at

        return response()->json([
            'message' => 'Post updated successfully',
            'data'    => $post->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $post = BlogPost::find($id);

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}