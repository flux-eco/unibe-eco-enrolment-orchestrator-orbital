<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Configs;

use UnibeEco\EnrolmentOrchestratorOrbital\Configs;

class Settings
{
    private function __construct(
        public Configs\SoapServerSettings $soapServerSettings,
        public string     $omnitrackerServerHost,
        public ActionParameters           $actionParameters
    )
    {

    }

    public static function new(
        Configs\SoapServerSettings $soapServerSettings,
        string $omnitrackerServerHost,
    ): self
    {
        return new self($soapServerSettings, $omnitrackerServerHost, ActionParameters::new($soapServerSettings, $omnitrackerServerHost));
    }
}