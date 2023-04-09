<?php

namespace FluxEco\ObjectMapper\Types;

final readonly class MappingOptions
{
    private function __construct(
        public bool                                                           $printSrcAttributesWhileMapping,
        public ?MappingOptionSetStateAttributeValueIfSourceAttributeNotExists $setStateValueIfSourceKeyNotExists,
    )
    {

    }

    public static function new(
        bool                                                           $printSrcAttributesWhileMapping = false,
        ?MappingOptionSetStateAttributeValueIfSourceAttributeNotExists $setStateValueIfSourceKeyNotExists = null
    ): self
    {
        return new self(...get_defined_vars());
    }
}