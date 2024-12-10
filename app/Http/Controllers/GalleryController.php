<?php

namespace App\Http\Controllers;

use App\Services\Gallery\GalleryService;

class GalleryController extends Controller
{
    public function getPage(GalleryService $galleryService)
    {
        return $galleryService->getGalleryPage();
    }
}
