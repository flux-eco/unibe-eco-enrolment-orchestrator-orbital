<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class ReferenceType
{

    private function __construct(
        public ObjectId   $parentObjectId,
        public ObjectType $referencedObjectType
    ) {
    }

    static function new(
        ObjectId   $parentObjectId,
        ObjectType $referencedObjectType
    ) : self {

        return new self(
          ...get_defined_vars()
        );
    }

    public function jsonSerialize() : array
    {
        return (array) $this;
    }
}