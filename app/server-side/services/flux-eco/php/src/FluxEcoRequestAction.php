<?php

namespace FluxEcoType;

final readonly class FluxEcoRequestAction
{
    private function __construct(
        public string                    $name,
        public string                    $path,
        public array|object              $parameters,
        public Types\FluxEcoResponseDefinition $responseDefinition
    )
    {

    }

    public static function new(
        string                    $name,
        string                    $path,
        array|object              $parameters,
        Types\FluxEcoResponseDefinition $responseDefinition
    ): self
    {
        return new self(...get_defined_vars());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameters(): array|object
    {
        return $this->parameters;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getResponseDefinition(): Types\FluxEcoResponseDefinition
    {
        return $this->responseDefinition;
    }
}