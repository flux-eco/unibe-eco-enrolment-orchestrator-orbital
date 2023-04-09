<?php

namespace FluxEcoType;

class FluxEcoResponseDefinition
{
    private function __construct(
        public ?string                $contentType,
        public ?FluxEcoAttributeDefinition $data
    )
    {

    }

    public static function new(
        ?string                $contentType = null,
        ?FluxEcoAttributeDefinition $data = null
    ): self
    {
        return new self(...get_defined_vars());
    }
}