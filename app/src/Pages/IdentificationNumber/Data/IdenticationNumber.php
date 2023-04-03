<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\IdentificationNumber\Data;

final readonly class IdenticationNumber implements \JsonSerializable
{
    private function __construct(
        public string $identicationNumber
    )
    {

    }

    public static function new(string $identicationNumber)
    {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize(): array
    {
        return ["identification-number" => $this->identicationNumber];
    }
}