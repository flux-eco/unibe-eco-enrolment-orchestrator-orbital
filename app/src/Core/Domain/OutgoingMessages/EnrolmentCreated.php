<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class EnrolmentCreated
{
    private function __construct(
        public ValueObjects\EnrolmentData $data
    )
    {

    }

    public static function new(
        ValueObjects\EnrolmentData $data
    ) : self
    {
        return new self(
            ...get_defined_vars()
        );
    }
}