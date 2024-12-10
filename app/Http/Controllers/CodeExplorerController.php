<?php

namespace App\Http\Controllers;

use App\Services\CodeExplorerService;
use App\Traits\LoggerTrait;
use Illuminate\Http\Request;

class CodeExplorerController extends Controller
{
    use LoggerTrait;

    private CodeExplorerService $codeExplorerService;

    public function __construct(CodeExplorerService $codeExplorerService)
    {
        $this->codeExplorerService = $codeExplorerService;
    }

    public function getRootDirPage(Request $request)
    {
        return $this->codeExplorerService->getExplorerView($request, 'code.common');
    }

    public function getDirOrFilePage(string $path, Request $request)
    {
        return $this->codeExplorerService->getExplorerView($request, 'code.common', $path);
    }

    public function getNotFoundPage(Request $request)
    {
        return $this->codeExplorerService->getNotFoundPage($request);
    }

    public function getHighLightedFileForExplorerPage(Request $request, ?string $path = null)
    {
        return $this->codeExplorerService->getHighLightedFileForExplorerPage($path, $request->get('file'));
    }
}
