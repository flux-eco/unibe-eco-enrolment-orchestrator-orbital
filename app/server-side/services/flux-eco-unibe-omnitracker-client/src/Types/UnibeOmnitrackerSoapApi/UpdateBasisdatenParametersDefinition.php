<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType;

final readonly class UpdateBasisdatenParametersDefinition
{

    private function __construct(
        public FluxEcoType\FluxEcoAttributeDefinition $pSessionId,
        public FluxEcoType\FluxEcoAttributeDefinition $pObjBasisdaten,
        public DefaultParametersDefinition            $defaultParametersDefinition,
    )
    {

    }

    public static function new(BaseDataItemAttributesDefinition $baseDataItemDefinition)
    {
        return new self(
            FluxEcoType\FluxEcoAttributeDefinition::new("pSessionId", "string"),
            FluxEcoType\FluxEcoAttributeDefinition::new("pObjBasisdaten", "object"),
            DefaultParametersDefinition::new(),
        );
    }

}