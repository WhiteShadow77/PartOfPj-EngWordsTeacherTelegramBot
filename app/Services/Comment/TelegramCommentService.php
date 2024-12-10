<?php

namespace App\Services\Comment;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class TelegramCommentService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function postComment(string $chatId, string $text, ?string &$errorMessage = null)
    {

        $isValidated = $this->validatePostComment($text, $errorMessage);

        if ($isValidated === true) {
            $userModel = $this->userModel->where('chat_id', $chatId)->first();

            $commentsQuantity = $userModel->comments()->count();

            if ($commentsQuantity < config('comment.max_quantity_limit')) {
                $userModel->comments()->create([
                    'text' => $text
                ]);
                return true;
            } else {
                $errorMessage = 'Reached the limit of the ability to post comments';
            }
        } else {
            return false;
        }
    }

    private function validatePostComment(mixed $comment, ?string &$errorMessage = null)
    {
        $rules = ['comment' => ['required', 'string', 'min:2', 'max:512']];
        $messages = [
            'min' => 'Comment must have more then 1 symbols',
            'max' => 'Comment must not have more then 255 symbols',
        ];

        $validator = Validator::make(['comment' => $comment], $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return false;
        } else {
            return true;
        }
    }
}
