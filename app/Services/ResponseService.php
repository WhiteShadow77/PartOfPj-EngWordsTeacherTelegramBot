<?php

namespace App\Services;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ResponseResourceCollection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\Collection;

class ResponseService
{
    private array $responseData;

    private function setMessageFieldIfNotNull(?string $message)
    {
        if (!is_null($message)) {
            $this->responseData['message'] = $message;
        }
    }

    public function successResponse(?string $message = null)
    {
        $this->responseData['status'] = 'success';
        $this->setMessageFieldIfNotNull($message);

        return response()->json($this->responseData);
    }

    public function errorResponse(?string $message = null, ?int $code = null)
    {
        $this->responseData['status'] = 'error';
        $this->setMessageFieldIfNotNull($message);

        return response()->json($this->responseData, $code ?? 200);
    }

    public function successResponseWithKeyValueData(array $keyValueData, ?string $message = null)
    {
        $this->responseData['status'] = 'success';
        $this->setMessageFieldIfNotNull($message);
        $result = array_merge($this->responseData, $keyValueData);

        return response()->json($result);
    }

    public function errorResponseWithKeyValueData(array $keyValueData, ?string $message = null, ?int $code = null)
    {
        $this->responseData['status'] = 'error';
        $this->setMessageFieldIfNotNull($message);
        $result = array_merge($this->responseData, $keyValueData);

        return response()->json($result, $code ?? 200);
    }

    public function errorResponseWithException(string $message, int $code = 400)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => $message,
            ], $code)
        );
    }

    public function successResponseWithResourceCollection(
        string $message,
        string $resourceClassName,
        $collectionToResponse
    ) {
        $resourceInstance = new $resourceClassName($collectionToResponse);
        $resourceCollectionClassName = $resourceClassName . 'Collection';
        return new $resourceCollectionClassName(true, $message, $resourceInstance);
    }

    public function errorResponseWithExceptionAndKeyValueData(string|array $error, array $keyValueData, int $code = 400)
    {
        $response = [
                'status' => 'error',
                'error' => $error
            ];

            $response['data'] = $keyValueData;

        throw new HttpResponseException(
            response()->json($response, $code)
        );
    }
}
