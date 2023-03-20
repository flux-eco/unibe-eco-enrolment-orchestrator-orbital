<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\ObjectId;

final readonly class EntityReferenceKey
{

    private function __construct(
        public ObjectId $entityId,
        public ObjectType $referencedEntityType,
    )
    {
    }

    static function new(
        ObjectId $entityId,
        ObjectType $referencedEntityType,
    ): self
    {

        return new self(
            $entityId,
            $referencedEntityType
        );
    }

}