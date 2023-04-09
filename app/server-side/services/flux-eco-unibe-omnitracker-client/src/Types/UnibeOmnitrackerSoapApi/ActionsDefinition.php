<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType\FluxEcoAttributeDefinition;
use FluxEcoType\FluxEcoResponseDefinition;
use FluxEcoType\FluxEcoActionDefinition;

final readonly class ActionsDefinition
{
    private function __construct(
        public FluxEcoActionDefinition $getListStudienberechtigungsausweis,
        public FluxEcoActionDefinition $getListStudienberechtigungsausweistyp,
        public FluxEcoActionDefinition $getListKanton,
        public FluxEcoActionDefinition $getListGemeinde,
        public FluxEcoActionDefinition $getListSchuleMaturitaet,
        public FluxEcoActionDefinition $getListStaat,
        public FluxEcoActionDefinition $getListAnrede,
        public FluxEcoActionDefinition $createBasisdaten,
        public FluxEcoActionDefinition $updateBasisdaten,
    )
    {

    }

    public static function new(BaseDataItemAttributesDefinition $baseDataAttributeDefinition): self
    {
        return new self(
            FluxEcoActionDefinition::new("GetListStudienberechtigungsausweis", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListStudienberechtigungsausweisResult", "object"))),
            FluxEcoActionDefinition::new("GetListStudienberechtigungsausweistyp", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListStudienberechtigungsausweistypResult", "object"))),
            FluxEcoActionDefinition::new("GetListKanton", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListKantonResult", "object"))),
            FluxEcoActionDefinition::new("GetListGemeinde", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListGemeindeResult", "object"))),
            FluxEcoActionDefinition::new("GetListSchuleMaturitaet", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListSchuleMaturitaetResult", "object"))),
            FluxEcoActionDefinition::new("GetListStaat", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListStaatResult", "object"))),
            FluxEcoActionDefinition::new("GetListAnrede", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListAnredeResult", "object"))),
            FluxEcoActionDefinition::new("CreateBasisdaten", "Studis/AnmeldungStudium.svc?wsdl", CreateBasisdatenParametersDefinition::new(), FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("CreateBasisdatenResult", "object"))),
            FluxEcoActionDefinition::new("SaveBasisdaten", "Studis/AnmeldungStudium.svc?wsdl", UpdateBasisdatenParametersDefinition::new($baseDataAttributeDefinition), FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("SaveBasisdatenResult", "object"))),
        );
    }
}