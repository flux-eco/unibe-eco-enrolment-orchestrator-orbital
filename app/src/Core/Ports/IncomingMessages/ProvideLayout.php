<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\IncomingMessages;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
final readonly class ProvideLayout
{

    private function __construct(
        public string $valueObjectsConfigDirectoryPath
    ) {

    }

    public static function new(
        string $valueObjectsConfigDirectoryPath
    ) : self {

        return new self(
            ...get_defined_vars()
        );
    }
}