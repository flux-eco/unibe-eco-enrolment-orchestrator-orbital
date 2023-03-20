<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;

final readonly class ObjectId
{

    private function __construct(
        public string $id,
        public ObjectType $objectType,
    )
    {
    }

    static function new(
        string     $id,
        ObjectType $objectType,
    ): self
    {

        return new self(
            $id, $objectType
        );
    }

}