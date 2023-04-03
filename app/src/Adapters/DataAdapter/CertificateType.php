<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Data;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types\Label;

final readonly class CertificateType implements UniversityEntranceQualification\Types\CertificateType
{
    /**
     * @param string $id
     * @param Label $label
     * @param bool $municipalityRequired
     */
    private function __construct(
        public string            $id,
        public Label $label,
        public bool              $municipalityRequired,
    )
    {

    }

    /**
     * @param string $id
     * @param Label $label
     * @param bool $municipalityRequired
     * @return static
     */
    public static function new(
        string            $id,
        Label $label,
        bool              $municipalityRequired,
    ): self
    {
        return new self($id, $label, $municipalityRequired);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }

    public function getMunicipalityRequired(): bool
    {
        return $this->municipalityRequired;
    }
}