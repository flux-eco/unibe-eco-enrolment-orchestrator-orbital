<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Omnitracker;


final readonly class QuerySettings
{
    private function __construct(public string $actionName, public object $actionParameters)
    {

    }

    public static function new(string $actionName,  object $actionParameters): QuerySettings
    {
        return new self(...get_defined_vars());
    }
}


