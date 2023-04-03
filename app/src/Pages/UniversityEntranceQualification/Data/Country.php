<?php


namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Data;


final readonly class Country
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
}