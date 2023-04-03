<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Schemas;

use stdClass;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\DataAdapter\Credentials;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\DataAdapter\ServerSettings;

final readonly class ReadActionSchemas
{
    private function __construct(
        public ActionSchema $getListStudienberechtigungsausweis,
        public ActionSchema $getListStudienberechtigungsausweistyp,
        public ActionSchema $getListKanton,
        public ActionSchema $getListGemeinde,
        public ActionSchema $getListSchuleMaturitaet,
        public ActionSchema $getListStaat,
    )
    {

    }

    public static function new(
        string $defaultLanguageCode = "de"
    ): self
    {
        return new self(
            ActionSchema::new("GetListStudienberechtigungsausweis", "Studis/Helptable.svc?wsdl", "GetListStudienberechtigungsausweisResult"),
            ActionSchema::new("GetListStudienberechtigungsausweistyp", "Studis/Helptable.svc?wsdl", "GetListStudienberechtigungsausweistypResult"),
            ActionSchema::new("GetListKanton", "Studis/Helptable.svc?wsdl", "GetListKantonResult"),
            ActionSchema::new("GetListGemeinde", "Studis/Helptable.svc?wsdl", "GetListGemeindeResult"),
            ActionSchema::new("GetListSchuleMaturitaet", "Studis/Helptable.svc?wsdl", "GetListSchuleMaturitaetResult"),
            ActionSchema::new("GetListStaat", "Studis/Helptable.svc?wsdl", "GetListStaatResult"),
        );
    }
}