<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType;

final readonly class DefaultParametersDefinition
{

    private function __construct(
        public FluxEcoType\FluxEcoAttributeDefinition $pOTServer,
        public FluxEcoType\FluxEcoAttributeDefinition $pOTUser,
        public FluxEcoType\FluxEcoAttributeDefinition $pOTPassword,
        public FluxEcoType\FluxEcoAttributeDefinition $pLanguagecode
    )
    {

    }

    public static function new()
    {
        return new self(
            FluxEcoType\FluxEcoAttributeDefinition::new("pOTServer", "string"),
            FluxEcoType\FluxEcoAttributeDefinition::new("pOTUser", "string"),
            FluxEcoType\FluxEcoAttributeDefinition::new("pOTPassword", "string"),
            FluxEcoType\FluxEcoAttributeDefinition::new("pLanguagecode", "string")
        );
    }

}