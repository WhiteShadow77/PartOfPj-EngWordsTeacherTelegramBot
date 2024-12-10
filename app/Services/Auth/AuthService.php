<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\GuestService;
use App\Traits\LoggerTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthService
{
    use LoggerTrait;

    private function isTelegramAuthorized(array $requestData): bool
    {
        $this->writeInfoLog('Authorization by telegram checking', [
            'request data' => $requestData
        ]);

        $hashFromRequest = $requestData['hash'];
        unset($requestData['hash']);

        $keyValueInItemBuffer = [];
        foreach ($requestData as $key => $value) {
            $keyValueInItemBuffer[] = $key . '=' . $value;
        }
        sort($keyValueInItemBuffer);
        $keyValueCheckString = implode("\n", $keyValueInItemBuffer);

        $this->writeInfoLog('Authorization by telegram checking', [
            'key-value check string' => $keyValueCheckString,
        ]);

        $secretKey = hash('sha256', config('bot.token'), true);
        $hMacHash = hash_hmac('sha256', $keyValueCheckString, $secretKey);
        $result = strcmp($hMacHash, $hashFromRequest) === 0;

        $this->writeInfoLog('Authorization by telegram checking', [
            'is telegram authorized' => $result,
        ]);
        return $result;
    }

    /**
     * @throws Exception
     */
    public function getLoginPage(Request $request, GuestService $guestService)
    {
        if (sizeof($request->all()) == 0) {
            $currentLanguageConfig = $guestService->getCurrentLanguageConfig();
            App::setLocale($currentLanguageConfig['currentLanguage']);
            return view('guest.auth.login-page', $currentLanguageConfig);
        } else {
            if ($this->isTelegramAuthorized($request->all())) {
                $userModel = User::where('telegram_user_id', $request->id)->first();
                if (!is_null($userModel)) {
                    if ($userModel->photo_url != $request->photo_url) {
                        $userModel->update(['photo_url' => $request->photo_url]);
                    }
                    Auth::login($userModel);
                    return redirect()->route('config');
                } else {
                    //throw new Exception('User not found');
                    return redirect()->route('unknown-user.page');
                }
            } else {
                return redirect()->route('login.page');
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        Cookie::queue(Cookie::forget('XSRF-TOKEN'));
        session()->invalidate();
        return redirect(route('login.page'));
    }

    public function getUnknownUserPage(GuestService $guestService)
    {
        $currentLanguageConfig = $guestService->getCurrentLanguageConfig();
        App::setLocale($currentLanguageConfig['currentLanguage']);

        return view('guest.auth.unknown-user-page', $currentLanguageConfig);
    }
}
