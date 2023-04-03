<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Configs;

use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Data;

final readonly class Outbounds
{
    private function __construct(
        public JsonFileReader $jsonFileReader,
        public RawDataReader  $rawDataReader
    )
    {

    }

    /**
     * @return self
     */
    public static function new(
        JsonFileReader $jsonFileReader,
        RawDataReader  $rawDataReader
    )
    {
        return new self(...get_defined_vars());
    }
}


interface JsonFileReader
{
    public function getAbsoluteFilePath(string $directoryPath, string $jsonFileName): string;

    public function readJsonFile(string $absoluteJsonFilePath): array|object;
}

interface RawDataReader
{
    /**
     * @return Data\CertificateType[]
     */
    public function readCertificateTypes(): array;

    /**
     * @return Data\Certificate[]
     */
    public function readCertificates(): array;

    /**
     * @return Data\Canton[]
     */
    public function readCantons(): array;

    /**
     * @return Data\Country[]
     */
    public function readCountries(): array;

    /**
     * @return Data\School[]
     */
    public function readSchools(): array;

    /**
     * @return Data\Locality[]
     */
    public function readMunicipalities(): array;

    /**
     * @return int
     */
    public function getCountrySwitzerlandUniqueId(): int;
}