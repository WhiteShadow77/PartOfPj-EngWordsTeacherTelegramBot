<?php

namespace App\Services\Auth\Api;

use App\Http\Requests\Api\ApiLoginRequest;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ApiAuthService
{
    private ResponseService $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function login(ApiLoginRequest $request)
    {
        if (Auth::attempt(
            [
                'email' => $request->email,
                'password' => $request->password
            ]
        )) {
            $user = Auth::user();
            $previousAccessTokensIds = PersonalAccessToken::where('tokenable_id', $user->id)
                ->pluck('id')
                ->toArray();
            PersonalAccessToken::destroy($previousAccessTokensIds);
            $token = $user->createToken(
                $user->first_name . ' ' . $user->last_name,
                $user->getRoleNames()->toArray()
            );
            $data = [
                'user first name' => $user?->first_name,
                'telegram user name' => $user?->telegram_user_name,
                'token type' => 'Bearer',
                'token' => $token->plainTextToken
            ];
            if(sizeof($user->getRoleNames()) > 0){
                $data['roles'] = $user->getRoleNames();
            }
            return $this->responseService->successResponseWithKeyValueData([
                'data' => $data
            ], 'User successfully logged in');
        } else {
            return $this->responseService->errorResponse(
                'Wrong login or password, or wrong login and password', 403
            );
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->responseService->successResponse('User successfully logged out');
    }
}
