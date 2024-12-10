<?php

namespace App\Services\Commands;

use App\Services\CodeExplorerService;
use App\Traits\LoggerTrait;

class DeleteLogsCopiesCommandService
{
    use LoggerTrait;

    public function __invoke(CodeExplorerService $codeExplorerService): void
    {
        $sourceFolder = storage_path('logs') . '/LogCopies';

        $this->writeInfoLog('Try to delete logs copies', [
            'source folder' => $sourceFolder
        ]);

        $fileNames = [];
        $fileQuantity = 0;

        foreach ($codeExplorerService->explore($sourceFolder) as $item) {
            if ($item['isFile']) {
                $fileNames[] = $item['value'];
                $fileQuantity++;
                unlink($sourceFolder . '/' . $item['value']);
            }
        }

        $this->writeInfoLog('Deleted logs copies', [
            'files' => $fileNames,
            'quantity' => $fileQuantity
        ], isAllowedSendToTlg: true);
    }
}
