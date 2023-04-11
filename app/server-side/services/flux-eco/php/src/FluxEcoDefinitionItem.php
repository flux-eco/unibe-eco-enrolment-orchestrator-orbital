<?php

namespace FluxEcoType;

final readonly class FluxEcoDefinitionItem
{
    public string $asPropertyName;

    /**
     * @param string $name
     * @param FluxEcoFilePathDefinition $stateFilePath
     */
    private function __construct(
        public string                    $name,
        public FluxEcoFilePathDefinition $stateFilePath,
    )
    {
        $this->asPropertyName = $this->toCamelCase($name);
    }

    public static function new(
        string                    $name,
        FluxEcoFilePathDefinition $stateFilePath
    )
    {
        return new self(...get_defined_vars());
    }

    public function toCamelCase(string $name) {
        $str = str_replace('-', ' ', $name);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);
        return lcfirst($str);
    }
}