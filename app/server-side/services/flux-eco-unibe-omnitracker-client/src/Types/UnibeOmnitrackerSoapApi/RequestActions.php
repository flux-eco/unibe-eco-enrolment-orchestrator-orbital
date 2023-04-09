<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType\FluxEcoRequestAction;
use FluxEcoType\FluxEcoBasicCredentials;
use FluxEcoType\FluxEcoServerBindingDefinition;

final readonly class RequestActions
{
    private function __construct(
        private ActionsDefinition              $actionsDefinition,
        private FluxEcoServerBindingDefinition $soapBindingDefinition,
        private array                          $defaultParameters
    )
    {

    }


    public static function new(
        ActionsDefinition $actionsDefinition,
        FluxEcoServerBindingDefinition $soapBindingDefinition,
        FluxEcoBasicCredentials        $omnitrackerCredentials,
        string                         $omnitrackerServerHost,
        string                         $defaultLanguageCode = "de"
    )
    {
        $parameters = [
            "pOTServer" => $omnitrackerServerHost,
            "pOTUser" => $omnitrackerCredentials->getUserName(),
            "pOTPassword" => $omnitrackerCredentials->getPassword(),
            "pLanguagecode" => $defaultLanguageCode
        ];
        return new self(
            $actionsDefinition,
            $soapBindingDefinition,
            $parameters
        );
    }

    public function getListStudienberechtigungsausweis(): FluxEcoRequestAction
    {
        return FluxEcoRequestAction::new(
            $this->actionsDefinition->getListStudienberechtigungsausweis->name,
            $this->createFullActionPath($this->actionsDefinition->getListStudienberechtigungsausweis->path),
            $this->defaultParameters,
            $this->actionsDefinition->getListStudienberechtigungsausweis->responseDefinition
        );
    }

    public function getListStudienberechtigungsausweistyp(): FluxEcoRequestAction
    {
        $parameters = $this->defaultParameters;
        $parameters['pType'] = 1;

        return FluxEcoRequestAction::new(
            $this->actionsDefinition->getListStudienberechtigungsausweistyp->name,
            $this->createFullActionPath($this->actionsDefinition->getListStudienberechtigungsausweistyp->path),
            $parameters,
            $this->actionsDefinition->getListStudienberechtigungsausweistyp->responseDefinition
        );
    }

    public function getListKanton(): FluxEcoRequestAction
    {
        return FluxEcoRequestAction::new(
            $this->actionsDefinition->getListKanton->name,
            $this->createFullActionPath($this->actionsDefinition->getListKanton->path),
            $this->defaultParameters,
            $this->actionsDefinition->getListKanton->responseDefinition
        );
    }

    public function getListGemeinde(): FluxEcoRequestAction
    {
        return FluxEcoRequestAction::new(
            $this->actionsDefinition->getListGemeinde->name,
            $this->createFullActionPath($this->actionsDefinition->getListGemeinde->path),
            $this->defaultParameters,
            $this->actionsDefinition->getListGemeinde->responseDefinition
        );
    }

    public function getListSchuleMaturitaet():FluxEcoRequestAction
    {
        return FluxEcoRequestAction::new(
            $this->actionsDefinition->getListSchuleMaturitaet->name,
            $this->createFullActionPath($this->actionsDefinition->getListSchuleMaturitaet->path),
            $this->defaultParameters,
            $this->actionsDefinition->getListSchuleMaturitaet->responseDefinition
        );
    }

    public function getListStaat(): FluxEcoRequestAction
    {
        return FluxEcoRequestAction::new(
            $this->actionsDefinition->getListStaat->name,
            $this->createFullActionPath($this->actionsDefinition->getListStaat->path),
            $this->defaultParameters,
            $this->actionsDefinition->getListStaat->responseDefinition
        );
    }

    private function createFullActionPath(string $relativeActionPath): string
    {
        return sprintf('%s/%s', $this->soapBindingDefinition->toString(), $relativeActionPath);
    }
}