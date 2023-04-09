<?php

namespace FluxEcoType;

use JsonSerializable;

final readonly class FluxEcoTransactionResponse implements JsonSerializable
{

    private function __construct(
        public string  $transactionId,
        public ?string $lastCompletedAction,
        public bool    $ok,
        public array   $errorMessages
    )
    {

    }

    public static function new(
        string  $transactionId,
        ?string $lastCompletedAction,
        bool    $ok,
        array   $errorMessages = []
    ): self
    {
        return new self(...get_defined_vars());
    }

    public function toJson(): string {
        return json_encode($this);
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}