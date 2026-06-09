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

    public function shouldRenderJson(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    public function render($request, Throwable $e): Response
    {
        $response = parent::render($request, $e);

        if (! $this->shouldRenderJson($request)) {
            return $response;
        }

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $this->errorResponse(
            message: $this->message($response),
            status: $response->getStatusCode()
        );
    }

    protected function message(Response $response): string
    {
        return Response::$statusTexts[$response->getStatusCode()] ?? 'Error';
    }
}
