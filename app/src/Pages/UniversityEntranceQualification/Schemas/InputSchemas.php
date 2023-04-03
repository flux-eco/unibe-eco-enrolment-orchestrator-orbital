<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Schemas;

use  UnibeEco\EnrolmentOrchestratorOrbital\Adapters\InputsAdapter;
use JsonSerializable;

final readonly class InputSchemas implements JsonSerializable
{
    private function __construct(
        public InputsAdapter\InputSchema $certificateTypes,
        public InputsAdapter\InputSchema $certificatesIssueYears,
        public InputsAdapter\InputSchema $certificates,
        public InputsAdapter\InputSchema $maturaCanton,
        public InputsAdapter\InputSchema $upperSecondarySchool,
        public InputsAdapter\InputSchema $certificateCountries,
        public InputsAdapter\InputSchema $certificateCanton,
        public InputsAdapter\InputSchema $municipalities,
    )
    {
    }

    public static function new(): self
    {
        return new self(
            InputsAdapter\InputSchema::new("certificate-types", 0),
            InputsAdapter\InputSchema::new("certificates-issue-years", 1),
            InputsAdapter\InputSchema::new("certificates", 2),
            InputsAdapter\InputSchema::new("matura-canton", 3),
            InputsAdapter\InputSchema::new("upper-secondary-school", 4),
            InputsAdapter\InputSchema::new("certificateCountries", 5),
            InputsAdapter\InputSchema::new("certificate-canton", 6),
            InputsAdapter\InputSchema::new("municipalities", 7),
        );
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}