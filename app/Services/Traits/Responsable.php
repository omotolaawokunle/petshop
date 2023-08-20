<?php

namespace App\Services\Traits;

use App\Services\ResponseCodes;


trait Responsable
{
    public function success($data, $statusCode = 200, $headers = [])
    {
        return response()->json([
            'data' => $data,
            'success' => 1,
            'error' => null,
            'errors' => [],
        ], $statusCode, $headers);
    }

    public function error($message, array $errors = [], array $data = [], $statusCode = 400, $headers = [])
    {
        return response()->json([
            'success' => 0,
            'error' => $message,
            'errors' => $errors,
            'data' => $data
        ], $statusCode, $headers);
    }

    public function notFound(string $message = "")
    {
        $message = $message ?? "Resource not found!";
        return $this->error(
            message: $message,
            statusCode: ResponseCodes::HTTP_NOT_FOUND
        );
    }

    public function validationError($errors = [])
    {
        return $this->error(
            message: "Failed Validation",
            errors: $errors,
            statusCode: ResponseCodes::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
