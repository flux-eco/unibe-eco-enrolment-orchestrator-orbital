<?php

namespace FluxEcoType;

final readonly class FluxEcoAttributeDefinition
{
    /**
     * @param string $name
     * @param string $type
     * @param FluxEcoFilePathDefinition|null $schemaFilePath
     */
    private function __construct(
        public string                      $name,
        public string                      $type,
        public ?FluxEcoFilePathDefinition  $schemaFilePath,
    )
    {

    }

    public static function new(
        string                     $name,
        string                     $type,
        ?FluxEcoFilePathDefinition $schemaFilePath = null,
    )
    {
        return new self(...get_defined_vars());
    }
}