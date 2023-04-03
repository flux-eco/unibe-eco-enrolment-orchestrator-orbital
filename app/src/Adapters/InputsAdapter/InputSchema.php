<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\InputsAdapter;

use JsonSerializable;

final readonly class InputSchema implements JsonSerializable
{
    private function __construct(
        public string $inputName,
        public int    $optionItemListIndex
    )
    {

    }

    public static function new(
        string $inputName,
        int    $optionItemListIndex
    ): self
    {
        return new self($inputName, $optionItemListIndex);
    }

    public function jsonSerialize(): array
    {
        return [$this->inputName, $this->optionItemListIndex];
    }
}