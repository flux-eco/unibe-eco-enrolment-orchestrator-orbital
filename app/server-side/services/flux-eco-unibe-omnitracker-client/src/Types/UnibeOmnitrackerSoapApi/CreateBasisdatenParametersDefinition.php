<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType;

final readonly class CreateBasisdatenParametersDefinition
{

    private function __construct(
        public FluxEcoType\FluxEcoAttributeDefinition $pSessionId,
        public FluxEcoType\FluxEcoAttributeDefinition $pUserPassword,
        public DefaultParametersDefinition            $defaultParametersDefinition,
    )
    {

    }

    public static function new()
    {
        return new self(
            FluxEcoType\FluxEcoAttributeDefinition::new("pSessionId", "string"),
            FluxEcoType\FluxEcoAttributeDefinition::new("pUserPassword", "string"),
            DefaultParametersDefinition::new(),
        );
    }

}