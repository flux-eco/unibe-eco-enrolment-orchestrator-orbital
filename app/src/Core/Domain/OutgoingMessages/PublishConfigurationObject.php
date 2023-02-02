<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\ConfigurationObjectName;
use JsonSerializable;

final readonly class PublishConfigurationObject
{
    private function __construct(
        public object $configurationObject
    )
    {

    }

    public static function new(
        object $configurationObject
    ) : self
    {
        return new self(
            ...get_defined_vars()
        );
    }
}