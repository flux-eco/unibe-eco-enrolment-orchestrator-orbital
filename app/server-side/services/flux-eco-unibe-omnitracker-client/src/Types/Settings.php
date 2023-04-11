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
        public array                                     $degreeProgramSubjectFilter,
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

        $degreeProgramSubjectFilter[] = UnibeOmnitrackerSoapApi\DegreeProgramBfsCodeMapping::new( 745096, [15,16]); //bachelor
        $degreeProgramSubjectFilter[] = UnibeOmnitrackerSoapApi\DegreeProgramBfsCodeMapping::new(  745097, [25]); //master
        $degreeProgramSubjectFilter[] = UnibeOmnitrackerSoapApi\DegreeProgramBfsCodeMapping::new(  745099, [56]); //minormob bachelor

        return new self(
            $soapBindingDefinition,
            $omnitrackerCredentials,
            $actionsDefinition,
            $omnitrackerCredentials,
            $omnitrackerServerHost,
            DefaultParametersDefinition::new(),
            $degreeProgramSubjectFilter,
            $defaultLanguageCode
        );
    }
}