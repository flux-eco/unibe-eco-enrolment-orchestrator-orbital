<?php

namespace FluxEcoType;

final readonly class  FluxEcoTransactionStateObject
{
    private function __construct(
        public string  $transactionId,
        public ?object $data,
        public array  $handledPageNamesCurrentWorkflow,
        public ?string $currentPageName,
        public ?int    $expiration
    )
    {

    }

    public static function createNew(
        string  $transactionId,
        ?object $data = null,
        array  $handledPageNamesCurrentWorkflow = [],
        ?string $currentPageName = null,

    ): self
    {
        return new self(
            $transactionId, $data, $handledPageNamesCurrentWorkflow, $currentPageName, time() + 3600 // Expiration: 1 hour after the last storage operation
        );
    }

    public static function fromCachedState(
        string  $transactionId,
        ?object $data = null,
        array  $handledPageNamesCurrentWorkflow = [],
        ?string $currentPageName = null,
        int     $expiration = null
    ): self
    {
        return new self(
            ...get_defined_vars()
        );
    }
}