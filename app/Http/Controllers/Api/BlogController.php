<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    // ✅ Semua blog (public)
    public function index()
    {
        return response()->json(['data' => Blog::latest()->get()]);
    }

    // ✅ GET detail blog by ID
    public function show($id)
    {
        $blog = Blog::with([
            'comments' => function ($query) {
                $query->latest()->with('user:id,name,email');
            },
        ])
            ->withCount('comments')
            ->findOrFail($id);

        return response()->json(['data' => $blog]);
    }
    public function showBySlug($slug)
    {
        $blog = Blog::with([
            'comments' => function ($query) {
                $query->latest()->with('user:id,name,email');
            },
        ])
            ->withCount('comments')
            ->where('slug', $slug)
            ->first();

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        return response()->json(['data' => $blog]);
    }



    // ✅ Tambah blog (admin)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string',
            'content' => 'required|string',
            'category' => 'nullable|string',
            'author_email' => 'nullable|email',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:20480',
        ]);

        $path = $request->hasFile('image')
            ? $request->file('image')->store('blogs', 'public')
            : null;

        $blog = Blog::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? Str::slug($validated['title']),
            'content' => $validated['content'],
            'category' => $validated['category'] ?? 'Uncategorized',
            'author_email' => $validated['author_email'] ?? $request->user()->email,
            'published_at' => now(),
            'comments_count' => 0,
            'image' => $path,
        ]);

        return response()->json(['message' => 'Blog created', 'data' => $blog], 201);
    }

    // ✅ Update blog (admin)
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string',
            'content' => 'required|string',
            'category' => 'nullable|string',
            'author_email' => 'nullable|email',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:20480',
        ]);

        if ($request->hasFile('image')) {
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $validated['image'] = $request->file('image')->store('blogs', 'public');
        }

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $blog->update($validated);

        return response()->json(['message' => 'Blog updated', 'data' => $blog]);
    }

    // ✅ Hapus blog (admin)
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
