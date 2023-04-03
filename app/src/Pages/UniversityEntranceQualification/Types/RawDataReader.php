<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types;

interface RawDataReader
{
    /**
     * @return CertificateType[]
     */
    public function readCertificateTypes(): array;

    /**
     * @return Certificate[]
     */
    public function readCertificates(): array;

    /**
     * @return Canton[]
     */
    public function readCantons(): array;

    /**
     * @return Country[]
     */
    public function readCountries(): array;

    /**
     * @return School[]
     */
    public function readSchools(): array;

    /**
     * @return Locality[]
     */
    public function readMunicipalities(): array;

    /**
     * @return int
     */
    public function getCountrySwitzerlandUniqueId(): int;
}