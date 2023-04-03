<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Configs;

use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Types;

class Settings
{
    private function __construct(
        public Types\SoapServerSettings $soapServerSettings,
        public string $omnitrackerServerHost,
        public ActionParameters           $actionParameters
    )
    {

    }

    public static function new(
        Types\SoapServerSettings $soapServerSettings,
        string $omnitrackerServerHost,
    ): self
    {
        return new self($soapServerSettings, $omnitrackerServerHost, ActionParameters::new($soapServerSettings, $omnitrackerServerHost));
    }
}