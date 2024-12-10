<?php

namespace App\Http\Middleware;

use App\Services\ResponseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;


class ApiSanctumAbilities
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle($request, Closure $next, ...$abilities)
    {
        $responseService = App::make(ResponseService::class);
        if(sizeof($abilities) === 0) {
            return $responseService->errorResponse('No abilities for access', 409);
        }
        foreach ($abilities as $ability) {
            if (!$request->user()->tokenCan($ability)) {
                return $responseService->errorResponse('Access denied', 403);
            }
        }

        return $next($request);
    }
}
