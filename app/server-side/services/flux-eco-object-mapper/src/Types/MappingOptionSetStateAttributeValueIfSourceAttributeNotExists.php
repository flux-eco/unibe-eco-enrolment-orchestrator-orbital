<?php

namespace FluxEco\ObjectMapper\Types;

class MappingOptionSetStateAttributeValueIfSourceAttributeNotExists
{
    private function __construct(public object $stateObjectAttributes)
    {

    }

    public static function new(object $stateObjectAttributes): self
    {
        return new self(...get_defined_vars());
    }
}