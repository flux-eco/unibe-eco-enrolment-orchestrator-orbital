<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\LanguageCode;

final readonly class LocalizedStringValue implements \JsonSerializable
{
    private function __construct(
        public string $languageCode,
        public string $value,
    )
    {

    }

    public static function new(
        string $languageCode,
        string $value
    )
    {
        return new self($languageCode, $value);
    }

    public function jsonSerialize(): array
    {
        return [
            $this->languageCode => $this->value
        ];
    }
}