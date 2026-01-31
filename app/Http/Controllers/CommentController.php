<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, string $type, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $model = null;
        if ($type === 'blog_post') {
            $model = BlogPost::findOrFail($id);
        }
        
        if (!$model) {
            abort(404);
        }

        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->content = $request->content;
        $comment->parent_id = $request->parent_id;
        $comment->status = 'approved'; // Auto-approve for now
        
        $model->comments()->save($comment);

        return back()->with('success', 'Comment posted successfully!');
    }

    public function destroy(Comment $comment)
    {
        $user = Auth::user();
        
        // Determine if user is the owner of the post/resource
        $isPostOwner = false;
        if ($comment->commentable_type === BlogPost::class) {
            $post = $comment->commentable;
            if ($post && $post->author_id === $user->id) {
                $isPostOwner = true;
            }
        }

        // Allow if user is the comment owner OR if user is an admin OR if user is the post owner
        if ($user->id !== $comment->user_id && $user->role !== 'admin' && !$isPostOwner) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }
}
