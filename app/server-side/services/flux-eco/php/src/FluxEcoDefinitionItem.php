<?php

namespace FluxEcoType;

final readonly class FluxEcoDefinitionItem
{
    /**
     * @param string $name
     * @param FluxEcoFilePathDefinition $stateFilePath
     */
    private function __construct(
        public string                    $name,
        public FluxEcoFilePathDefinition $stateFilePath,
    )
    {

    }

    public static function new(
        string                    $name,
        FluxEcoFilePathDefinition $stateFilePath
    )
    {
        return new self(...get_defined_vars());
    }
}