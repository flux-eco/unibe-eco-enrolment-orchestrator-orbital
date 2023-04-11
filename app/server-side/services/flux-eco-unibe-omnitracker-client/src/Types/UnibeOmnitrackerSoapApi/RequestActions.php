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
            $this->actionsDefinition->GetListStudienberechtigungsausweis->name,
            $this->createFullActionPath($this->actionsDefinition->GetListStudienberechtigungsausweis->path),
            $this->defaultParameters,
            $this->actionsDefinition->GetListStudienberechtigungsausweis->responseDefinition
        );
    }

    public function getListStudienberechtigungsausweistyp(): FluxEcoRequestAction
    {
        $parameters = $this->defaultParameters;
        $parameters['pType'] = 1;

        return FluxEcoRequestAction::new(
            $this->actionsDefinition->GetListStudienberechtigungsausweistyp->name,
            $this->createFullActionPath($this->actionsDefinition->GetListStudienberechtigungsausweistyp->path),
            $parameters,
            $this->actionsDefinition->GetListStudienberechtigungsausweistyp->responseDefinition
        );
    }

    public function getListKanton(): FluxEcoRequestAction
    {
        return FluxEcoRequestAction::new(
            $this->actionsDefinition->GetListKanton->name,
            $this->createFullActionPath($this->actionsDefinition->GetListKanton->path),
            $this->defaultParameters,
            $this->actionsDefinition->GetListKanton->responseDefinition
        );
    }

    public function getListGemeinde(): FluxEcoRequestAction
    {
        return FluxEcoRequestAction::new(
            $this->actionsDefinition->GetListGemeinde->name,
            $this->createFullActionPath($this->actionsDefinition->GetListGemeinde->path),
            $this->defaultParameters,
            $this->actionsDefinition->GetListGemeinde->responseDefinition
        );
    }

    public function getListSchuleMaturitaet():FluxEcoRequestAction
    {
        return FluxEcoRequestAction::new(
            $this->actionsDefinition->GetListSchuleMaturitaet->name,
            $this->createFullActionPath($this->actionsDefinition->GetListSchuleMaturitaet->path),
            $this->defaultParameters,
            $this->actionsDefinition->GetListSchuleMaturitaet->responseDefinition
        );
    }

    public function getListStaat(): FluxEcoRequestAction
    {
        return FluxEcoRequestAction::new(
            $this->actionsDefinition->GetListStaat->name,
            $this->createFullActionPath($this->actionsDefinition->GetListStaat->path),
            $this->defaultParameters,
            $this->actionsDefinition->GetListStaat->responseDefinition
        );
    }

    private function createFullActionPath(string $relativeActionPath): string
    {
        return sprintf('%s/%s', $this->soapBindingDefinition->toString(), $relativeActionPath);
    }
}