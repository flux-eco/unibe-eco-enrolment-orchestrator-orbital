<?php

namespace FluxEcoType;

final readonly class FluxEcoTransactionStateObject
{
    private function __construct(
        public string  $transactionId,
        public ?object $data,
        public ?string $lastHandledPage,
        public ?int    $expiration
    )
    {

    }

    public static function createNew(
        string  $transactionId,
        ?object $data = null,
        ?string $lastHandledPage = null,

    ): self
    {
        return new self(
            $transactionId, $data, $lastHandledPage, time() + 3600 // Expiration: 1 hour after the last storage operation
        );
    }

    public static function fromCachedState(
        string  $transactionId,
        ?object $data = null,
        ?string $lastHandledPage = null,
        int     $expiration = null
    ): self
    {
        return new self(
            ...get_defined_vars()
        );
    }
}