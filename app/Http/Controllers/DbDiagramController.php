<?php

namespace App\Http\Controllers;

use App\Services\DbDiagramService;

class DbDiagramController extends Controller
{
    private DbDiagramService $dbDiagramService;

    public function __construct(DbDiagramService $dbDiagramService)
    {
        $this->dbDiagramService = $dbDiagramService;
    }

    public function getDbDiagram()
    {
        return $this->dbDiagramService->getDbDiagram();
    }

    public function getDbDiagramView()
    {
        return $this->dbDiagramService->getDbDiagramView();
    }
}
