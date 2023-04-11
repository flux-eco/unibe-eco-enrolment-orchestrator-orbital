<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi;

use FluxEcoType\FluxEcoAttributeDefinition;
use FluxEcoType\FluxEcoResponseDefinition;
use FluxEcoType\FluxEcoActionDefinition;

final readonly class ActionsDefinition
{
    private function __construct(
        public FluxEcoActionDefinition $GetListStudienberechtigungsausweis,
        public FluxEcoActionDefinition $GetListStudienberechtigungsausweistyp,
        public FluxEcoActionDefinition $GetListKanton,
        public FluxEcoActionDefinition $GetListGemeinde,
        public FluxEcoActionDefinition $GetListOrtschaft,
        public FluxEcoActionDefinition $GetListSchuleMaturitaet,
        public FluxEcoActionDefinition $GetListStaat,
        public FluxEcoActionDefinition $GetListAnrede,
        public FluxEcoActionDefinition $GetListKorrespondenzsprache,
        public FluxEcoActionDefinition $GetListMuttersprache,
        public FluxEcoActionDefinition $GetListSemester,
        public FluxEcoActionDefinition $getListStudienstruktur,
        public FluxEcoActionDefinition $GetListStrukturStudienprogramm,
        public FluxEcoActionDefinition $GetListStudiengangsversion,
        public FluxEcoActionDefinition $CreateBasisdaten,
        public FluxEcoActionDefinition $UpdateBasisdaten,
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
            FluxEcoActionDefinition::new("GetListOrtschaft", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListOrtschaftResult", "object"))),
            FluxEcoActionDefinition::new("GetListSchuleMaturitaet", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListSchuleMaturitaetResult", "object"))),
            FluxEcoActionDefinition::new("GetListStaat", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListStaatResult", "object"))),
            FluxEcoActionDefinition::new("GetListAnrede", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListAnredeResult", "object"))),
            FluxEcoActionDefinition::new("GetListKorrespondenzsprache", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListKorrespondenzspracheResult", "object"))),
            FluxEcoActionDefinition::new("GetListMuttersprache", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListMutterspracheResult", "object"))),
            FluxEcoActionDefinition::new("GetListSemester", "Studis/Helptable.svc?wsdl", null, FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListSemesterResult", "object"))),
            FluxEcoActionDefinition::new("getListStudienstruktur", "Studis/Studiengang.svc?wsdl", GetListStrukturParametersDefinition::new(), FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListStudienstrukturResult", "object"))),
            FluxEcoActionDefinition::new("GetListStrukturStudienprogramm", "Studis/Studiengang.svc?wsdl", GetListStrukturStudienprogrammParametersDefinition::new(), FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListStrukturStudienprogrammResult", "object"))),
            FluxEcoActionDefinition::new("GetListStudiengangsversion", "Studis/Studiengang.svc?wsdl", GetListStudiengangsversionParametersDefinition::new(), FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("GetListStudiengangsversionResult", "object"))),
            FluxEcoActionDefinition::new("CreateBasisdaten", "Studis/AnmeldungStudium.svc?wsdl", CreateBasisdatenParametersDefinition::new(), FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("CreateBasisdatenResult", "object"))),
            FluxEcoActionDefinition::new("SaveBasisdaten", "Studis/AnmeldungStudium.svc?wsdl", UpdateBasisdatenParametersDefinition::new($baseDataAttributeDefinition), FluxEcoResponseDefinition::new("application/json", FluxEcoAttributeDefinition::new("SaveBasisdatenResult", "object"))),
        );
    }
}