<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Repositories;

use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\SoapFile;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\SoapParameters;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\Repositories;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages\{
    Message,
    MessageName,
    CreateReferenceObject
};
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ReferenceObjects\{
    ReferenceObjectName,
    LabelValueReferenceObject,
    Qualification,
    DegreeProgramme,
    DegreeProgrammeType,
    Subject
};
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\{
    LanguageCode,
    Server,
    Credentials,
    Label,
    MandatoryType,
    ValueObjectName
};

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ReferenceObjects\{
    BaseData
};


use JsonSerializable;
use SoapFault;

class EnrolmentRepository implements Repositories\EnrolmentRepository
{
    private string $wsdlPathAnmeldungStudium;

    private function __construct(
        public Server      $soapWsdlServer,
        public string      $soapServerHost,
        public Credentials $soapCredentials,
    )
    {
        $this->wsdlPathAnmeldungStudium = "Studis/AnmeldungStudium.svc?wsdl";
    }

    public static function new(Server $soapWsdlServer, string $soapServerHost, Credentials $soapCredentials): self
    {
        return new self(...get_defined_vars());
    }

    public function create(string $sessionId, string $password, LanguageCode $languageCode): BaseData
    {
        $parameters = SoapParameters::new(
            $this->soapServerHost,
            $this->soapCredentials,
            $languageCode
        )->parameters;
        $parameters['pSessionId'] = $sessionId;
        $parameters['pUserPassword'] = $password;

        $result = (array)$this->getClient($this->wsdlPathAnmeldungStudium)->{'CreateBasisdaten'} (
            $parameters
        )->CreateBasisdatenResult;

        return BaseData::new(
            $result["AHV"],
            $result["Anrede"],
            $result["AnredeUniqueId"],
            $result["CountryCode"],
            $result["ElternAdresszusatz"],
            $result["ElternAnrede"],
            $result["ElternAnredeUniqueId"],
            $result["ElternLandUniqueId"],
            $result["ElternNachname"],
            $result["ElternOrt"],
            $result["ElternOrtUniqueId"],
            $result["ElternNachname"],
            $result["ElternPostfach"],
            $result["ElternPostfachVorhanden"],
            $result["ElternStrasse"],
            $result["ElternVorname"],
            $result["EmailPrivat"],
            $result["Geburtstag"],
            $result["GeneralNotes"],
            $result["HeimatortUniqueId"],
            $result["Identifikationsnummer"],
            $result["KorrespondenzspracheUniqueId"],
            $result["Land"],
            $result["LandUniqueId"],
            $result["LastCompletedController"],
            $result["Matrikelnummer"],
            $result["MobilitaetHeimuniUniqueId"],
            $result["MutterspracheUniqueId"],
            $result["Nachname"],
            $result["NationalitaetUniqueId"],
            $result["Parallelstudium"],
            $result["PostEltern"],
            $result["PostStudierend"],
            $result["PruefungsmisserfolgMajor"],
            $result["QualificationStudiesAtUniversityOfBern"],
            $result["RechnungEltern"],
            $result["RechnungStudierend"],
            $result["RegistrationCompleted"],
            $result["SemesterUniqueId"],
            $result["StudentAdresszusatz"],
            $result["StudentOrt"],
            $result["StudentOrtUniqueId"],
            $result["StudentPLZ"],
            $result["StudentPostfach"],
            $result["StudentPostfachVorhanden"],
            $result["StudentStrasse"],
            $result["Studiengangsversion"],
            $result["StudiengangsversionParallel"],
            $result["StudiengangsversionParallelReqEcts"],
            $result["StudiengangsversionParallelUniqueId"],
            $result["StudiengangsversionReqEcts"],
            $result["StudiengangsversionUniqueId"],
            $result["Studienstruktur"],
            $result["StudienstrukturParallel"],
            $result["StudienstrukturParallelReqEcts"],
            $result["StudienstrukturParallelUniqueId"],
            $result["StudienstrukturReqEcts"],
            $result["StudienstrukturUniqueId"],
            $result["StudienstufeBFS"],
            $result["StudienstufeUniqueId"],
            $result["StudierendenkategorieUniqueId"],
            $result["Telefon"],
            $result["TelefonTyp"],
            $result["UniqueId"],
            $result["VorbildungMasterabschluss"],
            $result["Vorname"],
            $result["Vorname2"],
            $result["Vorname3"],
            $result["WunschEinstufungsSemester"]
        );
    }

    public function storeBaseData(string $sessionId, object $baseData, LanguageCode $languageCode): object
    {
        $parameters = SoapParameters::new(
            $this->soapServerHost,
            $this->soapCredentials,
            $languageCode
        )->parameters;
        $parameters['pSessionId'] = $sessionId;
        $parameters['pObjBasisdaten'] = $baseData;

        $resultBasedata = $this->getClient($this->wsdlPathAnmeldungStudium)->{'SaveBasisdaten'} (
            $parameters
        )->SaveBasisdatenResult;

        return $resultBasedata;
    }

    private function getClient(string $wsdlFilePath): \SoapClient
    {
        echo $this->soapWsdlServer->toString() . "/" . $wsdlFilePath;
        try {
            return new \SoapClient($this->soapWsdlServer->toString() . "/" . $wsdlFilePath,
                ['connection_timeout' => '1']);
        } catch (SOAPFault $e) {
            print_r($e->faultstring);
        }
    }


}