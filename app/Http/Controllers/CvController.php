<?php

namespace App\Http\Controllers;

use App\Http\Requests\CvUpdateRequest;
use App\Services\CvService;
use App\Services\ResponseService;

class CvController extends Controller
{
    public function getCv(CvService $cvService)
    {
        return $cvService->getCvResponse();
    }

    public function updateCv(CvUpdateRequest $request, CvService $cvService, ResponseService $responseService)
    {
        return $cvService->updateCv($request->file('cv'), $responseService);
    }

    public function deleteCv(CvService $cvService, ResponseService $responseService)
    {
        return $cvService->deleteCv($responseService);
    }
}
