<?php

namespace FluxEco\UnibeEnrolment\Types;


final readonly class DataDirectories
{
    private function __construct(
        public string $pageDefinitionStates,
        public string $inputOptionsDefinitionStates,
        public string $layoutDefinitionStates
    )
    {

    }

    public static function new(
        string $dataDirectory
    ): self
    {
        return new self(
            implode("/", [$dataDirectory, "pages"]),
            implode("/", [$dataDirectory, "input-options"]),
            implode("/", [$dataDirectory, "layout"]),
        );
    }
}