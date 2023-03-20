<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class DegreeProgrammeType {
    private function __construct(
        public string             $id,
        public ValueObjects\Label $label,
        public array|null         $mandatory,
        public array|null         $singleChoice,
        public array|null         $multipleChoice
    ) {

    }
    public static function new(
        string             $id,
        ValueObjects\Label $label,
        array|null         $mandatory = null,
        array|null         $singleChoice = null,
        array|null         $multipleChoice = null
    ) {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize() : mixed
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'mandatory' => $this->mandatory,
            'single-choice' => $this->singleChoice,
            'multiple-choice' => $this->multipleChoice
        ];
    }
}