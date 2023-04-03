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

