<?php

namespace App\Services\Comment;

use App\Models\Comment;
use App\Traits\LoggerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Enums\CommentAction;

class CommentService
{
    use LoggerTrait;

    public function __construct()
    {
        $this->commentModel = new Comment();
    }

    public function getCommentTemplate()
    {
        $comments = $this->commentModel->all();
        return view('home.comment', ['comments' => $comments]);
    }

    public function postComment(Request $request, int $userId)
    {
        $this->validateCommentAction(CommentAction::post, $request);

        $commentsQuantity = $this->commentModel->where('user_id', $userId)->count();

        if ($commentsQuantity < config('comment.max_quantity_limit')) {
            $this->commentModel->create([
                'text' => $request->comment,
                'user_id' => $userId
            ]);

            $this->writeInfoLog('User has posted the comment', [
                'user id' => $userId,
                'text' => $request->comment
            ], isAllowedSendToTlg: true);

            return redirect()->route('comment');
        } else {
            $errorMessage =  __("Reached the limit of the ability to post comments") . '. ' .
                __("Max quantity of comments is") . ': ' . config('comment.max_quantity_limit');

            return back()->withErrors([(CommentAction::post)->name . 'CommentAlert' => $errorMessage]);
        }
    }

    public function deleteComment(int $id, int $userId)
    {
        $commentModel = $this->commentModel->where('id', $id)->first();

        $this->writeInfoLog('User has deleted the comment', [
            'user id' => $userId,
            'deleted text' => $commentModel->text
        ], isAllowedSendToTlg: true);

        $commentModel->delete();
        return redirect()->route('comment');
    }

    public function editComment(int $id, Request $request, int $userId)
    {
        $this->validateCommentAction(CommentAction::edit, $request);

        $this->commentModel->where('id', $id)->update(['text' => $request->comment]);

        $this->writeInfoLog('User has edited the comment', [
            'user id' => $userId,
            'new text' => $request->comment
        ], isAllowedSendToTlg: true);

        return redirect()->route('comment');
    }

    private function validateCommentAction(CommentAction $commentAction, Request $request)
    {
        $rules = ['comment' => ['required', 'string', 'min:2', 'max:255']];
        $messages = [
            'required' => 'Comment must not be empty',
            'min' => 'Comment must have more then 1 symbols',
            'max' => 'Comment must not have more then 255 symbols',
        ];

        $validator = Validator::make($request->input(), $rules, $messages);

        if ($validator->fails()) {
            $responseMessage[$commentAction->name . 'CommentError'] = current(current($validator->errors()));

            if ($commentAction == CommentAction::edit) {
                $responseMessage['comment_id'] = $request->id;
            }

            throw ValidationException::withMessages($responseMessage);
        }
    }
}
