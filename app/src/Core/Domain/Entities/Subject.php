<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class Subject {
    private function __construct(
        public string             $id,
        public ValueObjects\Label $label,
        public int                $ect,
        public array              $combinations = []
    ) {

    }

    public static function new(
        string             $id,
        ValueObjects\Label $label,
        int                $ect,
        array              $combinations = []
    ) {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize() : mixed
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'ect' => $this->ect,
            'combinations' => $this->combinations
        ];
    }
}