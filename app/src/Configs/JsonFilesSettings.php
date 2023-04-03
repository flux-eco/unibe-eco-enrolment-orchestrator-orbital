<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Configs;



final readonly class JsonFilesSettings
{
    private function __construct(
        public string $inputDataJsonFilesDirectoryPath,
        public string $pageStructureJsonFilesDirectoryPath,
    )
    {

    }

    public static function new(
        string $inputDataJsonFilesDirectoryPath,
        string $pageStructureJsonFilesDirectoryPath,
    )
    {
        return new self(
            ...get_defined_vars()
        );
    }
}