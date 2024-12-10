<?php

namespace App\Http\Middleware;

use App\Services\CodeExplorerService;
use App\Traits\LoggerTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CodeExplorerMiddlware
{
    use LoggerTrait;

    public function __construct(private CodeExplorerService $codeExplorerService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $path = $request->route('path');
        $routePathItems = explode('/', $path);

        if ($request->has('file')) {
            $filesOrDirs = array_merge($routePathItems, [$request->file]);
        } else {
            $filesOrDirs = $routePathItems;
        }

        $forbiddenFoldersOrFiles = array_intersect($filesOrDirs, config('code_explorer.forbidden'));

        //dd($forbiddenFoldersOrFiles);

        if (sizeof($forbiddenFoldersOrFiles) > 0) {
            $logData['target'] = array_values($forbiddenFoldersOrFiles);

            $this->writeInfoLog(
                'Try to see forbidden',
                $logData,
                isAllowedSendToTlg: true
            );

                $rootDir = $this->codeExplorerService->getRootDir(2);
                $dir = $rootDir . '/' . $path;

                return response(view(
                    'code.forbidden-view',
                    $this->codeExplorerService->getDataForExplorerPage($dir, $request, 'code.common', $path, true)
                ));
        } else {
            return $next($request);
        }
    }
}
