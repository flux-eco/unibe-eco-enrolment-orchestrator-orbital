<?php

namespace FluxEcoType;

use Closure;

class FluxEcoActionDefinition
{
    public string $schema = "https://flux-eco.fluxlabs.ch/schemas/flux-eco-action-schema.json";

    /**
     * @param string $name
     * @param string|Closure $path - The path can either be a string indicating the action path,
     * such as an HTTP request action, or a closure that takes the defined parameters in this definition as parameters.
     * @param ?object $parametersDefinition - An object with properties of type FluxEcoDataDefinition.
     * @param ?FluxEcoResponseDefinition $responseDefinition
     */
    private function __construct(
        public string                     $name,
        public string|Closure             $path,
        public ?object                    $parametersDefinition,
        public ?FluxEcoResponseDefinition $responseDefinition
    )
    {

    }

    /**
     * @param string $name
     * @param string|Closure $path
     * @param ?object $parametersDefinition
     * @param ?FluxEcoResponseDefinition $responseDefinition
     * @return self
     */
    public static function new(
        string                     $name,
        string|Closure             $path,
        ?object                    $parametersDefinition = null,
        ?FluxEcoResponseDefinition $responseDefinition = null
    ): self
    {
        return new self(...get_defined_vars());
    }
}