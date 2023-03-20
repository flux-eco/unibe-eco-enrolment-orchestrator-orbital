<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\ObjectId;

class LabelValueReferenceObject //final readonly
{
    private function __construct(
        public ObjectType         $objectType,
        public ObjectId           $objectId,
        public ValueObjects\Label $label
    )
    {

    }

    public static function new(
        ObjectType         $objectType,
        string             $id,
        ValueObjects\Label $label
    ): self
    {
        return new self($objectType, ObjectId::new($id, $objectType), $label);
    }
}