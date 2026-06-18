<?php

namespace Telefunction\Support\Exceptions;

use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Telefunction\Support\Traits\Http\SendsApiResponses;
use Throwable;

class ApiExceptionHandler extends Handler
{
    use SendsApiResponses;

    public function shouldReturnDefaultResponse(): bool
    {
        return config('app.debug') === true;
    }

    public function shouldRenderJson(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    public function render($request, Throwable $e): Response
    {
        $response = parent::render($request, $e);

        if ($this->shouldReturnDefaultResponse()) {
            return $response;
        }

        if (! $this->shouldRenderJson($request)) {
            return $response;
        }

        if ($e instanceof ApiHttpException) {
            /** @var array<string, mixed> $errors */
            $errors = $e->errors();

            /** @var array<string, mixed> $meta */
            $meta = $e->meta();

            /** @var array<string, string|string[]> $headers */
            $headers = $e->getHeaders();

            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: $e->getStatusCode(),
                errors: $errors,
                code: $e->errorCode(),
                meta: $meta,
                headers: $headers,
            );
        }

        return $this->errorResponse(statusCode: $response->getStatusCode());
    }
}
