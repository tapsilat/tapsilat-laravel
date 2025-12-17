<?php

namespace Tapsilat\Laravel\Exceptions;

use Exception;
use Tapsilat\APIException;

class TapsilatException extends Exception
{
    /**
     * The HTTP status code.
     */
    public int $statusCode;

    /**
     * The Tapsilat error code.
     */
    public int $apiCode;

    /**
     * The error message from Tapsilat.
     */
    public string $error;

    /**
     * Create a new exception instance.
     */
    public function __construct(int $statusCode, int $apiCode, string $error)
    {
        parent::__construct("Tapsilat API Error: {$error}", $apiCode);

        $this->statusCode = $statusCode;
        $this->apiCode = $apiCode;
        $this->error = $error;
    }

    /**
     * Create an instance from an APIException.
     */
    public static function fromApiException(APIException $e): self
    {
        return new self($e->statusCode, $e->code, $e->error);
    }

    /**
     * Get the exception as an array.
     */
    public function toArray(): array
    {
        return [
            'status_code' => $this->statusCode,
            'code' => $this->apiCode,
            'error' => $this->error,
        ];
    }
}
