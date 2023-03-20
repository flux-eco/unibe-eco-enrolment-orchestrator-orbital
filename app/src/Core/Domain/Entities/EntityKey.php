<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\ObjectId;

final readonly class EntityKey
{

    private function __construct(
        public string     $entityId,
        public ObjectType $entityType,
    )
    {
    }

    static function new(
        string     $entityId,
        ObjectType $entityType,
    ): self
    {

        return new self(
           ...get_defined_vars()
        );
    }

    public function jsonSerialize(): array
    {
        return (array)$this;
    }
}