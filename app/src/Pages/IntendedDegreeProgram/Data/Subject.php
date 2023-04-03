<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\IntendedDegreeProgram\Data;

use UnibeEco\EnrolmentOrchestratorOrbital\Pages\IntendedDegreeProgram\Types;

final readonly class Subject
{
    private function __construct(
        public string      $id,
        public Types\Label $label,
        public int         $ect,
        public array       $combinations = []
    )
    {

    }

    public static function new(
        string      $id,
        Types\Label $label,
        int         $ect,
        array       $combinations = []
    )
    {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'ect' => $this->ect,
            'combinations' => $this->combinations
        ];
    }
}