<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class OmnitrackerBinding
{
    private function __construct(
        public string                   $omnitrackerServer,
        public ValueObjects\Server      $omnitrackerSoapServer,
        public ValueObjects\Credentials $omnitrackerCredentials
    )
    {

    }

    public static function new(
        string                   $omnitrackerServer,
        ValueObjects\Server      $omnitrackerSoapServer,
        ValueObjects\Credentials $omnitrackerCredentials
    )
    {
        return new self(
            ...get_defined_vars()
        );
    }
}