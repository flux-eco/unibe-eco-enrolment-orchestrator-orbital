<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities\ObjectType;
use JsonSerializable;

final readonly class CreateReferenceObject
{
    private function __construct(
        public ObjectType $referenceObjectName
    )
    {

    }

    public static function new(
        ObjectType $referenceObjectName
    ) : self
    {
        return new self(
            ...get_defined_vars()
        );
    }

    public function jsonSerialize() : mixed
    {
        return array($this);
    }
}