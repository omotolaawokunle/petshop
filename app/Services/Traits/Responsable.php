<?php

namespace App\Services\Traits;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseCodes;

trait Responsable
{
    public function success($data, $statusCode = 200, $headers = [], $onlyData = false): JsonResponse
    {
        //Send only data
        if ($onlyData) {
            return response()->json($data, $statusCode, $headers);
        }
        return response()->json([
            'data' => $data,
            'success' => 1,
            'error' => null,
            'errors' => [],
        ], $statusCode, $headers);
    }

    public function error($message, array $errors = [], array $data = [], $statusCode = 400, $headers = []): JsonResponse
    {
        return response()->json([
            'success' => 0,
            'error' => $message,
            'errors' => $errors,
            'data' => $data
        ], $statusCode, $headers);
    }

    public function notFound(string $message = ""): JsonResponse
    {
        $message = $message ?? "Resource not found!";
        return $this->error(
            message: $message,
            statusCode: ResponseCodes::HTTP_NOT_FOUND
        );
    }

    public function validationError($errors = []): JsonResponse
    {
        return $this->error(
            message: "Failed Validation",
            errors: $errors,
            statusCode: ResponseCodes::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public function download($filePath, string $fileName): BinaryFileResponse
    {
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
