<?php


namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Data;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types\Label;

final readonly class Country implements UniversityEntranceQualification\Types\Country
{
    private function __construct(
        public string             $id,
        public Label $label,
        public string $code
    )
    {

    }

    public static function new(
        string             $id,
        Label $label,
        string $code
    ): self
    {
        return new self($id, $label, $code);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}