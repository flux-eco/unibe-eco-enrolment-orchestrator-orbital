<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class SubjectCombination {
    private function __construct(
        public string             $id,
        public ValueObjects\Label $label,
        public array              $mandatory,
        public array              $singleChoice,
        public array              $multipleChoice
    ) {

    }

    public static function new(
        string             $id,
        ValueObjects\Label $label,
        array              $mandatory,
        array              $singleChoice,
        array              $multipleChoice
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