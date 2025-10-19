<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function indexAll()
    {
        $comments = Comment::with(['user:id,name,email', 'blog:id,title'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }

    // Ambil semua komentar berdasarkan blog
    public function index($id)
    {
        $blog = Blog::findOrFail($id);

        $comments = Comment::where('blog_id', $id)
            ->with('user:id,name,email')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }


    // Tambah komentar baru
    public function store(Request $request)
    {
        $request->validate([
            'blog_id' => 'required|exists:blogs,id',
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'blog_id' => $request->blog_id,
            'content' => $request->content,
        ]);

        // Update jumlah komentar di tabel blogs
        $blog = Blog::find($request->blog_id);
        $blog->increment('comments_count');

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan',
            'data' => $comment->load('user:id,name,email'),
        ], 201);
    }
}
