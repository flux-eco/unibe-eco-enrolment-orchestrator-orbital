<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter;

use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Schemas;

final readonly class Config
{
    private function __construct(public Schemas\ReadActionSchemas $actionSchemas, public Configs\Settings $settings)
    {

    }

    public static function new(Schemas\ReadActionSchemas $actionSchemas, Configs\Settings $settings): Config
    {
        return new self(...get_defined_vars());
    }
}


