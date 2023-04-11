<?php

namespace FluxEcoType\FluxEcoExceptionDefinitions;

use Exception;
use Throwable;


class FluxEcoException extends Exception
{
    public bool $usableInClientSideContext = false;
    public string $transactionId;

    public ?string $coorespondingActionName = null;

    public function __construct($message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function createForClientSideContext(
        $message = "", string $transactionId, ?string $coorespondingActionName = null, $code = 0, Throwable $previous = null
    ): self
    {
        $exception = new static($message, $code, $previous);
        $exception->usableInClientSideContext = true;
        $exception->transactionId = $transactionId;
        $exception->coorespondingActionName = $coorespondingActionName;
        return $exception;
    }

    public static function createForServerSideContext(
        $message = "", string $transactionId, ?string $coorespondingActionName = null, $code = 0, Throwable $previous = null
    ): self
    {
        $exception = new static($message, $code, $previous);
        $exception->usableInClientSideContext = false;
        $exception->transactionId = $transactionId;
        $exception->coorespondingActionName = $coorespondingActionName;
        return $exception;
    }
}