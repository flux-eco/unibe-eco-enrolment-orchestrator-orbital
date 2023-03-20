<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\PropertyType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\ObjectId;

final readonly class EntityPropertyKey
{

    private function __construct(
        public EntityKey $entityKey,
        public PropertyType $propertyType
    )
    {
    }

    static function new(
        EntityKey $entityKey,
        PropertyType $propertyType,
    ): self
    {

        return new self(
            $entityKey,
            $propertyType
        );
    }
}