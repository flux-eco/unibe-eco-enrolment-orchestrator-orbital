<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\LanguageCode;

final readonly class Year
{
    private function __construct(
        public string $id,
        public Label  $label
    )
    {

    }

    public static function new(
        string $id,
    ): Year
    {
        return new self(
            $id,
            Label::newGermanLabel($id),
        );
    }
}