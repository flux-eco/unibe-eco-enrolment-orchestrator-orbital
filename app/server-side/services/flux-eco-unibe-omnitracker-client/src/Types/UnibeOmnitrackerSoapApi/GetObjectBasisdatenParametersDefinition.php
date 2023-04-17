<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType;

final readonly class GetObjectBasisdatenParametersDefinition
{

    private function __construct(
        public FluxEcoType\FluxEcoAttributeDefinition $pSessionId,
        public FluxEcoType\FluxEcoAttributeDefinition $pIdentification,
        public DefaultParametersDefinition            $defaultParametersDefinition,
    )
    {

    }

    public static function new(): self
    {
        return new self(
            FluxEcoType\FluxEcoAttributeDefinition::new("pSessionId", "string"),
            FluxEcoType\FluxEcoAttributeDefinition::new("pIdentification", "string"),
            DefaultParametersDefinition::new(),
        );
    }

}