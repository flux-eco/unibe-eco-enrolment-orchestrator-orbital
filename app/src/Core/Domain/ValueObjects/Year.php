<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\LanguageCode;

final readonly class Year
{
    private function __construct(
        public string $id,
        public array  $label
    )
    {

    }

    public static function new(
        string $id,
    ): Year
    {
        return new self(
            $id,
            [
                LocalizedStringValue::new(
                    LanguageCode::DE->value,
                    $id
                )
            ]
        );
    }
}