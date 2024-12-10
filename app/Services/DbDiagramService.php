<?php

namespace App\Services;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class DbDiagramService
{
    public function getDbDiagram()
    {
        $file = Storage::disk('public')->get('DB-diagram/EngTeacher.png');

        $response = Response::make($file, 200)->header('Content-type', 'image/png');

        return $response;
    }

    public function getDbDiagramView()
    {
        return view('code.db-diagram-view', ['dirName' => 'DB diagram image']);
    }
}
