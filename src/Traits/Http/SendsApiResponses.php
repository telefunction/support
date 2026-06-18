<?php

namespace Telefunction\Support\Traits\Http;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait SendsApiResponses
{
    /**
     * @param array<string, mixed> $payload
     * @param array<string, string|string[]> $headers
     */
    protected function buildResponse(array $payload, int $status, array $headers): JsonResponse
    {
        return response()->json(
            data: $payload,
            status: $status,
            headers: $headers,
            options: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    protected function messageResponse(int $status): string
    {
        return Response::$statusTexts[$status] ?? 'Unknown';
    }

    /**
     * @param array<string, mixed> $meta
     * @param array<string, string|string[]> $headers
     */
    protected function successResponse(
        mixed $data = null,
        ?string $message = null,
        int $status = Response::HTTP_OK,
        array $meta = [],
        array $headers = []
    ): JsonResponse {
        $payload = [
            'message' => $message ?? $this->messageResponse($status),
        ];

        if (! is_null($data)) {
            $payload['data'] = $data;
        }

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return $this->buildResponse(
            payload: $payload,
            status: $status,
            headers: $headers
        );
    }

    /**
     * @param array<string, mixed>|null $errors
     * @param array<string, mixed> $meta
     * @param array<string, string|string[]> $headers
     */
    protected function errorResponse(
        ?string $message = null,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?array $errors = null,
        ?string $code = null,
        array $meta = [],
        array $headers = []
    ): JsonResponse {
        $payload = [
            'message' => $message ?? $this->messageResponse($statusCode),
        ];

        if ($code !== null) {
            $payload['code'] = $code;
        }

        if ($errors !== null && $errors !== []) {
            $payload['errors'] = $errors;
        }

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return $this->buildResponse(
            payload: $payload,
            status: $statusCode,
            headers: $headers
        );
    }
}
