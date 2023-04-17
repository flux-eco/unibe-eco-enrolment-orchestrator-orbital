<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType;

final readonly class LoginParametersDefinition
{

    private function __construct(
        public FluxEcoType\FluxEcoAttributeDefinition $pSessionId,
        public FluxEcoType\FluxEcoAttributeDefinition $pIdentification,
        public FluxEcoType\FluxEcoAttributeDefinition $pUserPassword,
        public DefaultParametersDefinition            $defaultParametersDefinition,
    )
    {

    }

    public static function new(): self
    {
        return new self(
            FluxEcoType\FluxEcoAttributeDefinition::new("pSessionId", "string"),
            FluxEcoType\FluxEcoAttributeDefinition::new("pIdentification", "string"),
            FluxEcoType\FluxEcoAttributeDefinition::new("pUserPassword", "string"),
            DefaultParametersDefinition::new(),
        );
    }

}