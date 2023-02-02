<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ReferenceObjects\ReferenceObjectName;
use JsonSerializable;

final readonly class CreateReferenceObject
{
    private function __construct(
        public ReferenceObjectName $referenceObjectName
    )
    {

    }

    public static function new(
        ReferenceObjectName $referenceObjectName
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