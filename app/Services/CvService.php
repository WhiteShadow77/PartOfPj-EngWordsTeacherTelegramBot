<?php

namespace App\Services;

use App\Traits\LoggerTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class CvService
{
    use LoggerTrait;

    public function getCvResponse()
    {
        $filePathes = Storage::disk('public')->files('CV');

        if (sizeof($filePathes) > 0) {
            $this->writeInfoLog('Request for CV', ['cv' => 'present'], isAllowedSendToTlg: true);

            $file = Storage::disk('public')->get(current($filePathes));
            $this->writeInfoLog('Opening CV', [], isAllowedSendToTlg: true);
            return Response::make($file, 200)
                ->header('Content-type', 'application/pdf')
                ->header('Content-disposition','attachment; filename="PHP_Dev_AlexK_CV"');
        } else {
            $this->writeInfoLog('Request for CV', ['cv' => 'not present'], isAllowedSendToTlg: true);
            //abort(404);
            return view('not-found-view', [
                'text' => 'Sorry. CV is not available at this time.'
            ]);
        }
    }

    public function upDateCv(UploadedFile $file, ResponseService $responseService)
    {
        $this->deleteFilesInCvDirectory();

        $fileName = $file->getClientOriginalName();
        $file->storeAs('public/CV', $fileName);

        return $responseService->successResponse('CV updated');
    }

    public function deleteCv(ResponseService $responseService)
    {
        if ($this->deleteFilesInCvDirectory()) {
            return $responseService->successResponse('CV has deleted');
        } else {
            return $responseService->successResponse('No CV to delete');
        }
    }

    private function deleteFilesInCvDirectory()
    {
        $fileNames = Storage::disk('public')->allFiles('CV');

        if (sizeof($fileNames) > 0) {
            foreach ($fileNames as $fileName) {
                Storage::disk('public')->delete($fileName);
            }

            return true;
        } else {
            return false;
        }
    }
}
