<?php

namespace App\Services\Traits;

use App\Services\ResponseCodes;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait Responsable
{
    /**
     * Return success response
     *
     * @param  mixed        $data
     * @param  int      $statusCode
     * @param  array<string|array|int|bool|float>        $headers
     *
     * @return JsonResponse
     */
    public function success(
        mixed $data,
        int $statusCode = 200,
        array $headers = [],
        bool $onlyData = false
    ): JsonResponse
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

    /**
     * Return error response
     *
     * @param  array<string|array|int|bool|float>        $errors
     * @param  array<string|array|int|bool|float>        $data
     * @param  array<string|array|int|bool|float>        $headers
     * @return JsonResponse
     */
    public function error(
        string $message,
        array $errors = [],
        array $data = [],
        int $statusCode = 400,
        array $headers = []
    ): JsonResponse
    {
        return response()->json([
            'success' => 0,
            'error' => $message,
            'errors' => $errors,
            'data' => $data,
        ], $statusCode, $headers);
    }

    public function notFound(string $message = ""): JsonResponse
    {
        $message = $message !== "" ? $message : "Resource not found!";
        return $this->error(
            message: $message,
            statusCode: ResponseCodes::HTTP_NOT_FOUND
        );
    }

    public function validationError(mixed $errors = []): JsonResponse
    {
        return $this->error(
            message: "Failed Validation",
            errors: $errors,
            statusCode: ResponseCodes::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public function download(string $filePath, string $fileName): BinaryFileResponse
    {
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
