<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\Portrait\Types;

final readonly class MinPasswordLength implements \JsonSerializable
{
    private function __construct(
        public int $minPasswordLength
    )
    {

    }

    public static function new(int $minPasswordLength): self
    {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize(): mixed
    {
        return ["min-password-length" => $this->minPasswordLength];
    }
}