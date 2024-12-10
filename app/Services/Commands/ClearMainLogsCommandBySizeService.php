<?php

namespace App\Services\Commands;

use App\Traits\LoggerTrait;

class ClearMainLogsCommandBySizeService
{
    use LoggerTrait;

    public function __invoke(int $fileSizeParam): void
    {
        $logSourceFile = storage_path('logs') . '/app.log';
        $logFileSize = filesize($logSourceFile);

        $this->writeInfoLog('Try to clear main logs file', [
            'file size param in Bytes' => $fileSizeParam,
            'file size in Bytes' => $logFileSize
        ]);

        if ($logFileSize >= $fileSizeParam) {
            //unlink($logSourceFile);
            file_put_contents($logSourceFile, '');

            $this->writeInfoLog('Cleared main logs file', [
                'file size param in Bytes' => $fileSizeParam,
                'file size in Bytes' => $logFileSize
            ], isAllowedSendToTlg: true);
        }
    }
}
