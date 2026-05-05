<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        return $post->comments()->with('user')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post)
    {
        $fields = $request->validate([
            'content' => 'required|string'
        ]);

        $comment = $post->comments()->create([
            'content' => $fields['content'],
            'user_id' => $request->user()->id
        ]);

        return $comment->load('user');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post, Comment $comment)
    {
        return $comment->load('user');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post, Comment $comment)
    {
        if ($request->user()->cannot('update', $comment)) {
            return response(['message' => 'Unauthorized'], 403);
        }

        $fields = $request->validate([
            'content' => 'required|string'
        ]);

        $comment->update($fields);

        return $comment->load('user');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post, Comment $comment)
    {
        if ($request->user()->cannot('delete', $comment)) {
            return response(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return ['message' => 'Comment deleted'];
    }
}
