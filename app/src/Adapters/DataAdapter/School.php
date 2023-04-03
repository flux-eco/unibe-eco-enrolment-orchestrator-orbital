<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Data;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types\Label;

final readonly class School implements UniversityEntranceQualification\Types\School
{
    private function __construct(
        public string             $id,
        public Label $label,
        public string             $schoolTypeId,
        public string             $certificateId,
        public string             $cantonId
    )
    {

    }

    /**
     * @param string $id
     * @param Label $label
     * @return static
     */
    public static function new(
        string             $id,
        Label $label,
        string             $schoolTypeId,
        string             $certificateId,
        string             $cantonId
    ): self
    {
        return new self($id, $label, $schoolTypeId, $certificateId, $cantonId);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }

    public function getSchoolTypeId(): string
    {
        return $this->schoolTypeId;
    }

    public function getCertificateId(): string
    {
        return $this->certificateId;
    }

    public function getCantonId(): string
    {
        return $this->cantonId;
    }
}