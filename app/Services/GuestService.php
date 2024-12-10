<?php

namespace App\Services;

use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\App;

class GuestService
{
    use LoggerTrait;

    public function getMainPage()
    {
        $currentLanguageConfig = $this->getCurrentLanguageConfig();
        $currentLanguage = $currentLanguageConfig['currentLanguage'];
        App::setLocale($currentLanguage);

        $this->writeInfoLog('Opened main page', [
            'current language' => $currentLanguage
        ], isAllowedSendToTlg: true);

        return view('guest.main-page', [
            'languages' => $currentLanguageConfig['languages'],
            'currentLanguage' => $currentLanguage
        ]);
    }

    public function setLanguage(string $language, ResponseService $responseService)
    {
        if (in_array($language, config('language.available_kinds'))) {
            session(['language' => $language]);

            return $responseService->successResponseWithKeyValueData([
                'data' => [
                    'language' => $language
                ]
            ], 'Has set language for guest');
        } else {
            return $responseService->errorResponseWithKeyValueData([
                'data' => [
                    'language' => $language
                ]
            ], 'This language is temporarily unsupported', 400);
        }
    }

    public function getCurrentLanguageConfig()
    {
        $currentLanguage = session()->get('language');
        $languages = config('language.for_guest_pages');
        return [
            'languages' => $languages,
            'currentLanguage' => $currentLanguage
        ];
    }
}
