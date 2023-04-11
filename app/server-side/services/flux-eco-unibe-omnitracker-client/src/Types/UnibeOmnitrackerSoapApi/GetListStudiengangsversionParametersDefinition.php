<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType\FluxEcoAttributeDefinition;

final readonly class GetListStudiengangsversionParametersDefinition
{
    private function __construct(
        public FluxEcoAttributeDefinition $pApplication,
        public FluxEcoAttributeDefinition $pBfsCodeStufe
    )
    {

    }

    public static function new(): self
    {
        return new self(
            FluxEcoAttributeDefinition::new("pApplication", "int"),
            FluxEcoAttributeDefinition::new("pBfsCodeStufe", "string")
        );
    }
}