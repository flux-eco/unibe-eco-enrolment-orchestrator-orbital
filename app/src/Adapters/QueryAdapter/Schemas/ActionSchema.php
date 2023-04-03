<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Schemas;

class ActionSchema
{
    private function __construct(
        public string $actionName,
        public string $wsdlFilePath,
        public string $responseObjectName
    )
    {

    }

    public static function new(
        string $actionName,
        string $wsdlFilePath,
        string $responseObjectName
    ): self
    {
        return new self(...get_defined_vars());
    }
}