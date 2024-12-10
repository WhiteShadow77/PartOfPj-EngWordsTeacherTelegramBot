<?php

namespace App\Http\Controllers;

use App\Services\Comment\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    private CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function getCommentTemplate()
    {
        return $this->commentService->getCommentTemplate();
    }

    public function postComment(Request $request)
    {
        $userId = Auth::id();
        return $this->commentService->postComment($request, $userId);
    }

    public function deleteComment(int $id)
    {
        $userId = Auth::id();
        return $this->commentService->deleteComment($id, $userId);
    }

    public function editComment(int $id, Request $request)
    {
        $userId = Auth::id();
        return $this->commentService->editComment($id, $request, $userId);
    }
}
