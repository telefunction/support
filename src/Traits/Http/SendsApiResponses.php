<?php

namespace Telefunction\Support\Traits\Http;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait SendsApiResponses
{
    /**
     * @param array<string, mixed> $meta
     * @param array<string, mixed> $headers
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'OK',
        int $status = Response::HTTP_OK,
        array $meta = [],
        array $headers = []
    ): JsonResponse {
        $payload = [
            'message' => $message,
        ];

        if (! is_null($data)) {
            $payload['data'] = $data;
        }

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return response()->json(
            data: $payload,
            status: $status,
            headers: $headers,
            options: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * @param array<string, mixed> $headers
     */
    protected function errorResponse(
        string $message = 'Error',
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR,
        mixed $errors = null,
        array $headers = []
    ): JsonResponse {
        $payload = [
            'message' => $message,
        ];

        if (! is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json(
            data: $payload,
            status: $status,
            headers: $headers,
            options: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
