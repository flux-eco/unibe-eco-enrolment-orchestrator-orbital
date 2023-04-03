<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\IntendedDegreeProgram\Schemas;

use JsonSerializable;

final readonly class InputSchemas implements JsonSerializable
{
    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}