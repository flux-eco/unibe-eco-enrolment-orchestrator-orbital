<?php

namespace FluxEco\UnibeEnrolment\Types;

use FluxEco\UnibeEnrolment\Types\Enrolment\EnrolmentDefinition;
use FluxEcoType\Workflow;

final readonly class Settings
{
    public EnrolmentDefinition $enrolmentDefinition;

    private function __construct(
        public DataDirectories $dataDirectories,
        public bool            $debug,

    )
    {
        $this->enrolmentDefinition = EnrolmentDefinition::new($dataDirectories);
    }

    public static function new(
        bool $debug = false
    ): self
    {
        return new self(
            DataDirectories::new(
                getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_DATA_DIRECTORY_PATH')
            ),
            $debug
        );
    }
}