<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType;
use FluxEcoType\FluxEcoAttributeDefinition;

final readonly class GetListStrukturStudienprogrammParametersDefinition
{
    private function __construct(
        public FluxEcoAttributeDefinition $pApplication,
        public FluxEcoAttributeDefinition $pPflichttyp
    )
    {

    }

    public static function new(): self
    {
        return new self(
            FluxEcoAttributeDefinition::new("pApplication", "int"),
            FluxEcoAttributeDefinition::new("pPflichttyp", "int"),
        );
    }
}