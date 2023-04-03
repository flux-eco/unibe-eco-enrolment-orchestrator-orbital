<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Data;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\Portrait\Types\Label;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification;

final readonly class Canton implements UniversityEntranceQualification\Types\Canton
{
    /**
     * @param string $id
     * @param Label $label
     */
    private function __construct(
        public string $id,
        public Label $label
    )
    {

    }

    /**
     * @param string $id
     * @param Label $label
     * @return static
     */
    public static function new(
        string $id,
        Label $label
    ): self
    {
        return new self($id, $label);
    }

    public function getId(): string
    {
       return $this->id;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }
}