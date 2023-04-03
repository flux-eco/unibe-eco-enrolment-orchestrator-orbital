<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Configs;

final readonly class Settings
{
    private function __construct(
        public string $inputDataJsonFileDirectoryPath,
        public string $pageStructureAbsoluteJsonFilePath,

    )
    {

    }

    public static function new(
        string $inputDataJsonFileDirectoryPath,
        string $pageStructureAbsoluteJsonFilePath
    )
    {
        return new self(
            $inputDataJsonFileDirectoryPath,
            $pageStructureAbsoluteJsonFilePath
        );
    }

}