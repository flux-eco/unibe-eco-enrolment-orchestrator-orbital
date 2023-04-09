<?php

namespace FluxEco\UnibeOmnitrackerClient\Types;

use FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi\BaseDataItemAttributesDefinition;
use FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi\DefaultParametersDefinition;
use FluxEcoType\FluxEcoBasicCredentials;
use FluxEcoType\FluxEcoServerBindingDefinition;

final readonly class Settings
{
    private function __construct(
        public FluxEcoServerBindingDefinition            $unibeOmnitrackerSoapApiBindingDefinition,
        public FluxEcoBasicCredentials                   $unibeOmnitrackerSoapApiCredentials,
        public UnibeOmnitrackerSoapApi\ActionsDefinition $unibeOmnitrackerSoapApiActionsDefinitions,
        public FluxEcoBasicCredentials                   $omnitrackerCredentials,
        public string                                    $omnitrackerServerHost,
        public DefaultParametersDefinition               $defaultActionParameterDefinitions,
        public string                                    $defaultLanguageCode = "de"
    )
    {

    }

    public static function new(
        FluxEcoServerBindingDefinition $soapBindingDefinition,
        FluxEcoBasicCredentials        $omnitrackerCredentials,
        string                         $omnitrackerServerHost,
        BaseDataItemAttributesDefinition $baseDataItemDefinition,
        string                         $defaultLanguageCode = "de"
    ): self
    {
        $actionsDefinition = UnibeOmnitrackerSoapApi\ActionsDefinition::new($baseDataItemDefinition);

        return new self(
            $soapBindingDefinition,
            $omnitrackerCredentials,
            $actionsDefinition,
            $omnitrackerCredentials,
            $omnitrackerServerHost,
            DefaultParametersDefinition::new(),
            $defaultLanguageCode
        );
    }
}