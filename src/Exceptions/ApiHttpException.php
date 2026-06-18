<?php

namespace Telefunction\Support\Exceptions;

use BadMethodCallException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * @phpstan-type ErrorBag array<string, mixed>
 * @phpstan-type HeaderBag array<string, string|string[]>
 *
 * @method static self badRequest(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 * @method static self unauthorized(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 * @method static self forbidden(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 * @method static self notFound(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 * @method static self methodNotAllowed(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 * @method static self conflict(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 * @method static self unprocessable(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 * @method static self tooManyRequests(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 * @method static self internalServerError(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 * @method static self serviceUnavailable(?string $message = null, ?string $errorCode = null, ErrorBag $errors = [], ErrorBag $meta = [])
 */
class ApiHttpException extends HttpException
{
    /**
     * @var array<string, int>
     */
    protected const STATUSES = [
        'badRequest' => Response::HTTP_BAD_REQUEST,
        'unauthorized' => Response::HTTP_UNAUTHORIZED,
        'forbidden' => Response::HTTP_FORBIDDEN,
        'notFound' => Response::HTTP_NOT_FOUND,
        'methodNotAllowed' => Response::HTTP_METHOD_NOT_ALLOWED,
        'conflict' => Response::HTTP_CONFLICT,
        'unprocessable' => Response::HTTP_UNPROCESSABLE_ENTITY,
        'tooManyRequests' => Response::HTTP_TOO_MANY_REQUESTS,
        'internalServerError' => Response::HTTP_INTERNAL_SERVER_ERROR,
        'serviceUnavailable' => Response::HTTP_SERVICE_UNAVAILABLE,
    ];

    /**
     * @param HeaderBag $headers
     * @param ErrorBag $errors
     * @param ErrorBag $meta
     */
    public function __construct(
        int $statusCode,
        ?string $message = null,
        ?Throwable $previous = null,
        array $headers = [],
        protected ?string $errorCode = null,
        protected array $errors = [],
        protected array $meta = [],
    ) {
        parent::__construct(
            statusCode: $statusCode,
            message: $message ?: $this->responseMessage($statusCode),
            previous: $previous,
            headers: $headers,
        );
    }

    /**
     * @param ErrorBag $errors
     * @param ErrorBag $meta
     * @param HeaderBag $headers
     */
    public static function make(
        int $statusCode,
        ?string $message = null,
        ?string $errorCode = null,
        array $errors = [],
        array $meta = [],
        array $headers = [],
        ?Throwable $previous = null,
    ): self {
        return new self(
            statusCode: $statusCode,
            message: $message,
            previous: $previous,
            headers: $headers,
            errorCode: $errorCode,
            errors: $errors,
            meta: $meta,
        );
    }

    /**
     * @param list<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): self
    {
        if (! array_key_exists($name, self::STATUSES)) {
            throw new BadMethodCallException("Method {$name} does not exist.");
        }

        $message = $arguments[0] ?? null;
        $errorCode = $arguments[1] ?? null;
        $errors = $arguments[2] ?? [];
        $meta = $arguments[3] ?? [];

        if ($message !== null && ! is_string($message)) {
            throw new InvalidArgumentException('The message must be a string or null.');
        }

        if ($errorCode !== null && ! is_string($errorCode)) {
            throw new InvalidArgumentException('The error code must be a string or null.');
        }

        if (! is_array($errors)) {
            throw new InvalidArgumentException('The errors must be an array.');
        }

        if (! is_array($meta)) {
            throw new InvalidArgumentException('The meta must be an array.');
        }

        /** @var ErrorBag $errors */
        /** @var ErrorBag $meta */

        return self::make(
            statusCode: self::STATUSES[$name],
            message: $message,
            errorCode: $errorCode,
            errors: $errors,
            meta: $meta,
        );
    }

    public function errorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * @return ErrorBag
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return ErrorBag
     */
    public function meta(): array
    {
        return $this->meta;
    }

    private function responseMessage(int $statusCode): string
    {
        return Response::$statusTexts[$statusCode] ?? 'Unknown';
    }
}
