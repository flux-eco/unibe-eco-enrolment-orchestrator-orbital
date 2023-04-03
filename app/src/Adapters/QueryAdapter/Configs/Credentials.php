<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Configs;

final readonly class Credentials
{
    private function __construct(
        public string $user,
        public string $password
    ) {

    }

    public static function new(
        string $user,
        string $password
    ) : self {
        return new self(...get_defined_vars());
    }
}