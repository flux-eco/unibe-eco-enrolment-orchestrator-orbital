<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums;

final readonly class SoapParameters
{
    private function __construct(
        public array $parameters
    )
    {

    }

    public static function new(
        string      $serverHost,
        ValueObjects\Credentials  $credentials,
        Enums\LanguageCode $languageCode
    ): self
    {
        return new self(
            [
                'pOTServer' => $serverHost,
                'pOTUser' => $credentials->user,
                'pOTPassword' => $credentials->password,
                'pLanguagecode' => $languageCode->value,
                'pFehler' => ''
            ]
        );
    }
}