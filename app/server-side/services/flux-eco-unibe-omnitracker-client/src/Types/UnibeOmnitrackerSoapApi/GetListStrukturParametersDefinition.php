<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType\FluxEcoAttributeDefinition;

final readonly class GetListStrukturParametersDefinition
{
    private function __construct(
        public FluxEcoAttributeDefinition $pApplication,
    )
    {

    }

    public static function new(): self
    {
        return new self(
            FluxEcoAttributeDefinition::new("pApplication", "int")
        );
    }
}