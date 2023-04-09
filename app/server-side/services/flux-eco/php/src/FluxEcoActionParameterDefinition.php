<?php

namespace FluxEcoType;

class FluxEcoActionParameterDefinition
{
    private function __construct(

    )
    {

    }

    public static function new(

    ): self
    {
        return new self(...get_defined_vars());
    }
}