<?php

namespace FluxEcoType;

final readonly class FluxEcoFilePathDefinition
{
    private function __construct(
        public string $directoryPath,
        public string $fileName,
        public string $contentType
    )
    {

    }

    public static function new(
        string $directoryPath,
        string $fileName
    )
    {
        return new self($directoryPath, $fileName, FluxEcoFileExtionsion::from(pathinfo($fileName, PATHINFO_EXTENSION))->toContentType());
    }
}