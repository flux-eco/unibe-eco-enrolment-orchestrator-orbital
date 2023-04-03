<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Configs;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Schemas;

final readonly class Config
{
    private function __construct(public Schemas\ReadActionSchemas $actionSchemas, public Settings $settings)
    {

    }

    public static function new(Schemas\ReadActionSchemas $actionSchemas, Settings $settings): Config
    {
        return new self(...get_defined_vars());
    }
}


