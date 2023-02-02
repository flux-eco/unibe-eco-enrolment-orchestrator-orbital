<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\ConfigurationObjectName;
use JsonSerializable;

final readonly class PublishPageObject
{
    private function __construct(
        public object $pageObject
    )
    {

    }

    public static function new(
        object $pageObject
    ) : self
    {
        return new self(
            ...get_defined_vars()
        );
    }
}