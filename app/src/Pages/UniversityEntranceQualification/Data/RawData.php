<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Data;

final readonly class RawData
{

    /**
     * @param CertificateType[] $certificateTypes
     * @param int[] $certificateIssueYears,
     * @param Certificate[] $certificates
     * @param Canton[] $cantons
     * @param School[] $schools
     * @param Country[] $countries
     * @param Locality[] $municipalities
     * @param int $countrySwitzerlandUniqueId
     * @param int[] $schoolCanton An associative array, using the school ID as the key and the school's canton ID as the value
     * @param int[][] $schoolCertificates A two-dimensional array of integers.
     * The first array contains a list of school IDs, and each second array contains a
     * list of certificate IDs issued by the corresponding school.
     * @param int[][] $cantonMuncipalities A two-dimensional array of integers.
     * The first array contains a list of Canton IDs, and each second array contains a
     * list of municipality IDs that correspond to the respective canton.
     */
    private function __construct(
        public array $certificateTypes,
        public array $certificateIssueYears,
        public array $certificates,
        public array $cantons,
        public array $schools,
        public array $countries,
        public array $municipalities,
        public int   $countrySwitzerlandUniqueId,
        public array $schoolCanton,
        public array $schoolCertificates,
        public array $cantonMuncipalities
    )
    {

    }


    /**
     * @param CertificateType[] $certificateTypes
     * @param Certificate[] $certificates
     * @param Canton[] $cantons
     * @param School[] $schools
     * @param Country[] $countries
     * @param Locality[] $municipalities
     * @return void
     */
    public static function new(
        array $certificateTypes,
        array $certificates,
        array $cantons,
        array $schools,
        array $countries,
        array $municipalities,
        int   $countrySwitzerlandUniqueId
    ): self
    {
        $schoolCanton = [];
        foreach ($schools as $school) {
            $schoolCanton[$school->id] = $school->cantonId;
        }
        $schoolCertificates = [];
        foreach ($schools as $school) {
            $schoolCertificates[$school->id][] = $school->certificateId;
        }

        $cantonMuncipalities = [];
        foreach ($municipalities as $municipality) {
            $cantonMuncipalities[$municipality->cantonId][] = $municipality->id;
        }


        $certificateIssueYears = [];
        foreach ($certificates as $certificate) {
            for ($issue_year = $certificate->minIssueYear; $issue_year <= $certificate->maxIssueYear; $issue_year++) {
                $certificateIssueYears[] = strval($issue_year);
            }
        }
        sort($certificateIssueYears);


        return new self(
            $certificateTypes,
            $certificateIssueYears,
            $certificates,
            $cantons,
            $schools,
            $countries,
            $municipalities,
            $countrySwitzerlandUniqueId,
            $schoolCanton,
            $schoolCertificates,
            $cantonMuncipalities
        );
    }


}