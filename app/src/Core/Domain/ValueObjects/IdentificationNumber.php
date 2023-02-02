<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class IdentificationNumber
{
    private function __construct(
        public string $value
    ) {

    }

    public static function new(string $value) : self
    {
        return new self($value);
    }
}