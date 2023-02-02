<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\IncomingMessages;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain;

final readonly class CreateEnrolmentConfiguration
{
    private function __construct(

    ) {

    }

    public static function new(

    ) : self {
        return new self(
            ...get_defined_vars()
        );
    }
}