<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth as AuthFacade;

class Authenticate extends Middleware
{
    public function __construct(AuthFactory $auth)
    {
        parent::__construct($auth);
        $language = AuthFacade::user()?->language;
        if ($language) {
            App::setLocale($language);
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login.page');
        }
    }
}
