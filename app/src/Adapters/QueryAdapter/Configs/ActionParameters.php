<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Configs;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Types;
use stdClass;

final readonly class ActionParameters
{
    private function __construct(
        public object $getListStudienberechtigungsausweistypParamaters,
        public object $getListStudienberechtigungsausweisParamaters,
        public object $getListKantonParamaters,
        public object $getListGemeindeParamaters,
        public object $getListSchuleMaturitaetParamaters,
        public object $getListStaatParamaters
    )
    {

    }


    public static function new(
        Types\SoapServerSettings $soapServerSettings,
        string                     $omnitrackerServerHost,
        string                     $defaultLanguageCode = "de"
    )
    {
        $actionParameters = new stdClass();
        $actionParameters->{"pOTServer"} = $omnitrackerServerHost;
        $actionParameters->{"pOTUser"} = $soapServerSettings->getSoapUser();
        $actionParameters->{"pOTPassword"} = $soapServerSettings->getSoapPassword();
        $actionParameters->{"pLanguagecode"} = $defaultLanguageCode;


        $getListStudienberechtigungsausweistypParamaters = $actionParameters;
        $getListStudienberechtigungsausweistypParamaters->pType = 1;

        return new self(
            $getListStudienberechtigungsausweistypParamaters,
            $actionParameters,
            $actionParameters,
            $actionParameters,
            $actionParameters,
            $actionParameters
        );
    }
}


