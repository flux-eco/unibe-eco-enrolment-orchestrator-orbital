<?php

namespace FluxEco\ObjectMapper\Types;

class MappingOptionPrefillFromCurrentState
{
    private function __construct(public object $stateObject)
    {

    }

    public static function new(object $stateObject): self
    {
        return new self(...get_defined_vars());
    }
}