<?php

namespace FluxEco\ObjectMapper\Types;

final readonly class MappingOptions
{
    private function __construct(
        public bool                                  $printSrcAttributesWhileMapping,
        public ?MappingOptionPrefillFromCurrentState $prefillFromCurrentState,
    )
    {

    }

    public static function new(
        bool                                  $printSrcAttributesWhileMapping = false,
        ?MappingOptionPrefillFromCurrentState $prefillFromCurrentState = null
    ): self
    {
        return new self(...get_defined_vars());
    }
}