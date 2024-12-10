<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGalleryItemRequest;
use App\Services\Gallery\FileService;
use App\Services\Gallery\ImageService;
use App\Services\Gallery\TinyPngService;
use App\Services\ResponseService;
use App\Services\Gallery\GalleryService;

class GalleryController extends Controller
{
    public function __construct(
        private GalleryService $galleryService
    ) {
    }

    public function updateItemByPositionNumber(
        int $position_number,
        UpdateGalleryItemRequest $request,
        ResponseService $responseService,
        ImageService $imageService,
        FileService $fileService,
        TinyPngService $tinyPngService
    ) {
        return $this->galleryService->updateItemByPositionNumber(
            $position_number,
            $responseService,
            $imageService,
            $fileService,
            $tinyPngService,
            $request->file('image'),
            $request->name,
            $request->description
        );
    }

    public function clearItemByPositionNumber(int $position_number, ResponseService $responseService)
    {
        return $this->galleryService->clearItemByPositionNumber($position_number, $responseService);
    }

    public function addItem(ResponseService $responseService)
    {
        return $this->galleryService->addItem($responseService);
    }

    public function deleteItemByPositionNumber(int $position_number, ResponseService $responseService)
    {
        return $this->galleryService->deleteItemByPositionNumber($position_number, $responseService);
    }
}
