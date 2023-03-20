<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use http\Exception\InvalidArgumentException;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\LanguageCode;

final readonly class Label
{
    private function __construct(
        public array $localizedValues
    )
    {

    }

    /**
     * @param LocalizedStringValue[] $localizedStringValues
     * @return Label
     */
    public static function new(
        array $localizedStringValues
    )
    {
        $translations = [];
        foreach ($localizedStringValues as $localizedString) {
            if (in_array($localizedString, $translations)) {
                continue;
            }
            if (LanguageCode::tryFrom($localizedString->languageCode) === null) {
                throw new InvalidArgumentException("Invalid language code: $localizedString->languageCode");
            }
            $translations[$localizedString->languageCode] = $localizedString->value;
        }

        return new self($translations);
    }

    public function get(LanguageCode $languageCode): string
    {
        if (array_key_exists($languageCode->value, $this->localizedValues)) {
            return $this->localizedValues[$languageCode->value];
        }

        return reset($this->localizedValues);
    }
}