<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Configs;

use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Schemas;


final readonly class Configs
{
    public string $pageStructureJsonFileName;
    public Settings $settings;

    private function __construct(
        public Outbounds       $outbounds,
        public string          $inputDataJsonFilesDirectoryPath,
        public string          $pageStructureJsonFilesDirectoryPath,
        public Schemas\Schemas $schemas,
    )
    {
        $this->pageStructureJsonFileName = "university-entrance-qualification.json";
        $this->settings = Settings::new(
            $inputDataJsonFilesDirectoryPath,
            $this->outbounds->jsonFileReader->getAbsoluteFilePath($pageStructureJsonFilesDirectoryPath, $this->pageStructureJsonFileName)
        );
    }

    public static function new(
        Outbounds $outbounds,
        string    $inputDataJsonFilesDirectoryPath,
        string    $pageStructureJsonFilesDirectoryPath,
    ): self
    {

        return new self(
            $outbounds,
            $inputDataJsonFilesDirectoryPath,
            $pageStructureJsonFilesDirectoryPath,
            Schemas\Schemas::new(
                Schemas\ActionSchemas::new(),
                Schemas\InputSchemas::new()
            )
        );
    }
}