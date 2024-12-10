<?php

namespace App\Services\Log;

use App\Models\Log;
use App\Services\ResponseService;
use App\Traits\LoggerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Predis\Client;

class LogService
{
    use LoggerTrait;

    private Client $redisClient;

    public function __construct()
    {
        $this->redisClient = new Client();
    }

    public function twitchIsEnabledWriteLog(ResponseService $responseService)
    {
        $logModel = Log::first();

        if ($logModel->is_enabled_write) {
            $newValue = false;
        } else {
            $newValue = true;
        }

        $logModel->update([
            'is_enabled_write' => $newValue
        ]);

        $this->redisClient->set('is_enabled_log_write', $newValue);

        $this->writeInfoLog('Log config the is_enabled_write changed', [
            'to' => $logModel->is_enabled_write
        ]);

        return $responseService->successResponseWithKeyValueData([
            'data' => [
                'current value' => $logModel->is_enabled_write
            ]
        ], 'Twitched the is_enabled_write_log value');
    }

    public function twitchIsEnabledSendLog(ResponseService $responseService)
    {
        $logModel = Log::first();

        if ($logModel->is_enabled_send) {
            $newValue = false;
        } else {
            $newValue = true;
        }

        $logModel->update([
            'is_enabled_send' => $newValue
        ]);

        $this->redisClient->set('is_enabled_log_send', $newValue);

        $this->writeInfoLog('Log config the is_enabled_send changed', [
            'to' => $logModel->is_enabled_send
        ]);

        return $responseService->successResponseWithKeyValueData([
            'data' => [
                'current value' => $logModel->is_enabled_send
            ]
        ], 'Twitched the is_enabled_send_log value');
    }

    public function getLogsConfig(ResponseService $responseService)
    {
        $logModel = Log::first();

        return $responseService->successResponseWithKeyValueData([
            'data' => [
                'is_enabled_log_send' => $logModel->is_enabled_send,
                'is_enabled_log_write' => $logModel->is_enabled_write,
                'created_at' => (new \DateTime($logModel->created_at))->format('Y-m-d H:i:s'),
                'updated_at' => (new \DateTime($logModel->updated_at))->format('Y-m-d H:i:s')
            ]
        ], 'Current logs config');
    }

    public function responseWithLogText(Request $request)
    {
        if (!$request->hasValidSignature()) {
            $this->writeInfoLog('Try of getting pron. archive by expired url');
            abort(403);
        }

        $file = Storage::disk('log')->get('app.log');

        $this->writeInfoLog('Request for log text', [], isAllowedSendToTlg: true);

        return Response::make($file, 200)->header('Content-type', 'text/plain');
    }

    public function getLogTextAccessLink(ResponseService $responseService)
    {
        $file = Storage::disk('log')->get('app.log');

        if ($file) {
            $this->writeInfoLog('Request for access text log link');

            $accessTimeMinutes = 1;
            $expireAt = now()->addMinutes($accessTimeMinutes);
            $temporaryLink = URL::temporarySignedRoute(
                'log-text-link',
                $expireAt
            );
            return $responseService->successResponseWithKeyValueData([
                'data' => [
                    'access_link' => $temporaryLink,
                    'expire_at' => $expireAt->format('Y-m-d H:i:s'),
                    'created_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        } else {
            $this->writeInfoLog('Request for access text log link failed', [
                'message' => 'log file not found or empty'
            ]);
            return $responseService->errorResponse('log file not found or empty', 404);
        }
    }

    public function clearLogFile(ResponseService $responseService)
    {
        $file = Storage::disk('log')->get('app.log');

        if ($file) {
            $this->writeInfoLog('Request for clearing log file');

            Storage::disk('log')->put('app.log', '');
            return $responseService->successResponse('log file cleared');
        } else {
            $this->writeInfoLog('Request for clearing log file failed', [
                'message' => 'log file not found or empty'
            ]);
            return $responseService->errorResponse('log file not found or empty', 404);
        }
    }
}
