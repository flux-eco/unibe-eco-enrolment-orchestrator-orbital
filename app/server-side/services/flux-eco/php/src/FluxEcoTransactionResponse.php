<?php

namespace FluxEcoType;

use JsonSerializable;

final readonly class FluxEcoTransactionResponse implements JsonSerializable
{

    private function __construct(
        public string  $transactionId,
        public ?string $context,
        public bool    $ok,
        public array   $errorMessages
    )
    {

    }

    public static function new(
        string  $transactionId,
        ?string $context,
        bool    $ok,
        array   $errorMessages = []
    ): self
    {
        return new self(...get_defined_vars());
    }

    public function toJson(): string
    {
        return json_encode($this);
    }

    public function jsonSerialize(): array
    {
        return [
            "transaction-id" => $this->transactionId,
            "context" => $this->context,
            "ok" => $this->ok,
            "error-messages" => $this->errorMessages,
        ];
    }
}