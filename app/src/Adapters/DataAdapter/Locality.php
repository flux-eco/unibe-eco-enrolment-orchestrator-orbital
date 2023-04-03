<?php
namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Data;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types\Label;

final readonly class Locality implements UniversityEntranceQualification\Types\Locality
{
    /**
     * @param string $id
     * @param Label $label
     * @param int $plz
     * @param string $cantonId
     */
    private function __construct(
        public string             $id,
        public Label $label,
        public int $plz,
        public string $cantonId
    )
    {

    }

    /**
     * @param string $id
     * @param Label $label
     * @param int $plz
     * @param string $cantonId
     * @return static
     */
    public static function new(
        string             $id,
        Label $label,
        int                $plz,
        string             $cantonId
    ): self
    {
        return new self($id, $label, $plz, $cantonId);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }

    public function getPlz(): int
    {
        return $this->plz;
    }

    public function getCantonId(): string
    {
        return $this->cantonId;
    }
}