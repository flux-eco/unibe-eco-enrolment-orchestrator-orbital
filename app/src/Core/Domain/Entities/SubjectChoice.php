<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\Label;

final readonly class SubjectChoice {
    private function __construct(
        public string $id,
        public Label  $label,
        public int    $ect,
        public array  $choices
    ) {

    }

    public static function new(
        string $id,
        Label  $label,
        int    $ect,
        array  $choices
    ): self {
        return new self(...get_defined_vars());
    }
}