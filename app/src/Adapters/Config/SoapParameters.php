<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

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
        ValueObjects\LanguageCode $languageCode
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