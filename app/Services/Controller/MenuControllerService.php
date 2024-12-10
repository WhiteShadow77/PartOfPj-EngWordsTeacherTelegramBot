<?php

namespace App\Services\Controller;

use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\App;

class MenuControllerService
{
    use LoggerTrait;

    public function getPage()
    {
        $this->writeInfoLog('Opened menu page', [], isAllowedSendToTlg: true);
        $currentLanguageConfig = $this->getCurrentLanguageConfig();
        $currentLanguage = $currentLanguageConfig['currentLanguage'];

        App::setLocale($currentLanguage);

        return view('guest.menu-page', [
            'languages' => $currentLanguageConfig['languages'],
            'currentLanguage' => $currentLanguage
        ]);
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

    public function getStackPage()
    {
        return view('guest.stack-page');
    }
}
