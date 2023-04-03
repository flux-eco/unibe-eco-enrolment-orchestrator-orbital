<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification;

final readonly class Page
{

    private function __construct(
        public Configs\Configs $configs
    )
    {

    }


    public static function new(
        Configs\Configs $configs
    ): self
    {
        return new self($configs);
    }

    public function writeInputJsonFiles(): void
    {
        $rawDataReader = $this->configs->outbounds->rawDataReader;

        $inputProvider = Inputs\InputsProvider::new(
            $this->configs->outbounds,
            $this->configs->settings->inputDataJsonFileDirectoryPath
        );

        $inputProvider->writeJsonFiles(
            Schemas\InputSchemas::new(),
            Data\RawData::new(
                $rawDataReader->readCertificateTypes(),
                $rawDataReader->readCertificates(),
                $rawDataReader->readCantons(),
                $rawDataReader->readSchools(),
                $rawDataReader->readCountries(),
                $rawDataReader->readMunicipalities(),
                $rawDataReader->getCountrySwitzerlandUniqueId()
            )
        );
    }
}