<?php

use FluxEco\UnibeEnrolment;
use FluxEco\JsonFileProcessor;
use FluxEco\HttpWorkflowRequestHandler;
use FluxEco\UnibeOmnitrackerClient;
use FluxEcoType\FluxEcoStateValues;

final readonly class Adapters
{
    public UnibeEnrolment\Api $unibeEnrolment;
    public HttpWorkflowRequestHandler\Api $httpTransactionGateway;
    public JsonFileProcessor\Api $jsonFileProcessor;
    public UnibeOmnitrackerClient\Api $omnitrackerClient;

    /**
     * @throws Exception
     */
    private function __construct(
        public Config $config
    )
    {
        $this->unibeEnrolment = $this->unibeEnrolmentApi($config->pageDirectoryPath, $config->inputOptionDataDirectory, $config->inputNames);
        $this->httpTransactionGateway = $this->httpTransactionGateway($config->layoutDirectoryPath, $this->config->layoutFileName);
        $this->jsonFileProcessor = $this->jsonFileProcessorApi();
        $this->omnitrackerClient = $this->omnitrackerClientApi();
    }

    public static function new(): Adapters
    {
        return new self(Config::new());
    }

    public function storeNewEnrolment(): Closure
    {
        return function (FluxEco\UnibeEnrolment\StateValues|stdClass $stateValues, string $transactionId, string $password) {
            $omnitrackerEnrolmentData = $this->omnitrackerClient->createEnrolment($transactionId, $password);
            return $this->mapOmnitrackerEnrolmentToUnibeEnrolment($omnitrackerEnrolmentData);
        };
    }

    public function updateEnrolment(): Closure
    {
        return function (FluxEco\UnibeEnrolment\StateValues|stdClass $stateValues, string $transactionId, object $validatedProcessData): object {
            $data = clone($stateValues->data->enrolmentData);
            foreach (get_object_vars($validatedProcessData) as $key => $value) {
                $tranformedKey = $this->toCamelCase($key);
                $data->{$tranformedKey} = $value;
            }
            $omnitrackerEnrolmentData = $this->mapUnibeEnrolmentOmnitrackerEnrolment($data);

            $stateNameUniversityEntranceQualification = $this->unibeEnrolment->readStateNames()->universityEntranceQualification;

            return match ($stateValues->currentStateName) {
                $stateNameUniversityEntranceQualification => [], //todo
                default => $this->mapOmnitrackerEnrolmentToUnibeEnrolment($this->omnitrackerClient->updateEnrolment($transactionId, $omnitrackerEnrolmentData))
            };


        };
    }

    public function readTransactionStateValuesFromManager(): Closure
    {
        return fn(stdClass|null $stateValues) => $this->unibeEnrolment->readTransactionStateValues($stateValues, $this->objectFromJsonFile());
    }

    public function processDataByTransactionStateManager(): Closure
    {
        return fn(stdClass|null $stateValues, object $processData) => $this->unibeEnrolment->processData($stateValues, $processData, $this->storeNewEnrolment(), $this->updateEnrolment(), $this->objectFromJsonFile());
    }

    public function objectFromJsonFile(): Closure
    {
        return fn(string $layoutDataDirectory, string $layoutFileName) => $this->jsonFileProcessor->readJsonFile($layoutDataDirectory, $layoutFileName);
    }

    public function readTransactionStateValuesFromCache(Swoole\Table $transactionDataCache): Closure
    {
        return function (?string $transactionId) use ($transactionDataCache): null|object {
            if ($transactionId === null || $transactionDataCache->get($transactionId) === false || array_key_exists('state', $transactionDataCache->get($transactionId)) === false) {
                return null;
            }
            return json_decode($transactionDataCache->get($transactionId)['state']);
        };
    }

    public function storeTransactionStateValuesInCache(Swoole\Table $transactionDataCache): Closure
    {


        return function ($transactionId, FluxEcoStateValues|stdClass $stateValues) use ($transactionDataCache) {
            $transactionDataCache->set($transactionId, [
                'stateId' => $transactionId,
                'state' => json_encode($stateValues),
                'expiration' => time() + 3600
            ]);

            echo "stored in cache" . PHP_EOL;

            print_r(json_decode($transactionDataCache->get($transactionId)['state']));
        };
    }


    public function readCookie($request): Closure
    {
        return function (string $cookieName) use ($request) {
            if (is_array($request->cookie) && array_key_exists($cookieName, $request->cookie)) {
                return $request->cookie[$cookieName];
            }
            return null;
        };
    }

    public function storeCookie($response): Closure
    {
        return function (string $cookieName, string $cookieValue) use ($response): void {
            $response->setCookie($cookieName, $cookieValue, time() + 3600);
        };
    }

    public function mapOmnitrackerEnrolmentToUnibeEnrolment(FluxEco\UnibeOmnitrackerClient\StateDataEnrolment|stdClass $omnitrackerEnrolmentData): FluxEco\UnibeEnrolment\StateDataEnrolment|stdClass
    {
        //todo specific mapping functions @see archive

        /**
         * @var FluxEco\UnibeEnrolment\StateDataEnrolment|stdClass $enrolmentData
         *
         * $enrolmentData->parentsAddressHouseNumber = $omnitrackerEnrolmentData->;
         */
        $enrolmentData = new stdClass();
        $enrolmentData->identificationNumber = $omnitrackerEnrolmentData->Identifikationsnummer;
        $enrolmentData->oldAgeSurvivorInsuranceNumber = $omnitrackerEnrolmentData->AHV;
        $enrolmentData->salutation = $omnitrackerEnrolmentData->Anrede;
        $enrolmentData->parentsAddressExtraAddressLine = $omnitrackerEnrolmentData->ElternAdresszusatz;
        $enrolmentData->parentsAddressSalutation = $omnitrackerEnrolmentData->ElternAnrede;
        $enrolmentData->parentsAddressCountry = $omnitrackerEnrolmentData->ElternLandUniqueId;
        $enrolmentData->parentsAddressLastName = $omnitrackerEnrolmentData->ElternNachname;
        $enrolmentData->parentsAddressPlace = $omnitrackerEnrolmentData->ElternOrtUniqueId;
        $enrolmentData->parentsAddressPostalCode = $omnitrackerEnrolmentData->ElternPLZ;
        $enrolmentData->parentsAddressStreet = $omnitrackerEnrolmentData->ElternStrasse;
        $enrolmentData->parentsAddressFirstNames = $omnitrackerEnrolmentData->ElternVorname;
        $enrolmentData->email = $omnitrackerEnrolmentData->EmailPrivat;
        $enrolmentData->birthDate = $omnitrackerEnrolmentData->Geburtstag;
        //$enrolmentData->GeneralNotes = $omnitrackerEnrolmentData->GeneralNotes;
        $enrolmentData->originPlace = $omnitrackerEnrolmentData->HeimatortUniqueId;
        $enrolmentData->correspondenceLanguage = $omnitrackerEnrolmentData->KorrespondenzspracheUniqueId;
        $enrolmentData->country = $omnitrackerEnrolmentData->LandUniqueId;
        $enrolmentData->lastCompletedController = $omnitrackerEnrolmentData->LastCompletedController;
        $enrolmentData->mobilitaetHeimuniUniqueId = $omnitrackerEnrolmentData->MobilitaetHeimuniUniqueId;
        $enrolmentData->motherLanguage = $omnitrackerEnrolmentData->MutterspracheUniqueId;
        $enrolmentData->lastName = $omnitrackerEnrolmentData->Nachname;
        $enrolmentData->nationally = $omnitrackerEnrolmentData->NationalitaetUniqueId;
        $enrolmentData->parallelstudium = $omnitrackerEnrolmentData->Parallelstudium;
        $enrolmentData->parentsAddressGeneralPost = $omnitrackerEnrolmentData->PostEltern;
        $enrolmentData->parentsAddressInvoices = $omnitrackerEnrolmentData->RechnungEltern;
        $enrolmentData->semester = $omnitrackerEnrolmentData->SemesterUniqueId;
        $enrolmentData->extraAddressLine = $omnitrackerEnrolmentData->StudentAdresszusatz;
        $enrolmentData->place = $omnitrackerEnrolmentData->StudentOrtUniqueId;
        $enrolmentData->postalCode = $omnitrackerEnrolmentData->StudentPLZ;
        $enrolmentData->postalOfficeBox = $omnitrackerEnrolmentData->StudentPostfach;
        $enrolmentData->street = $omnitrackerEnrolmentData->StudentStrasse;
        $enrolmentData->studiengangsversionParallelUniqueId = $omnitrackerEnrolmentData->StudiengangsversionParallelUniqueId;
        $enrolmentData->subject = $omnitrackerEnrolmentData->StudiengangsversionUniqueId;
        $enrolmentData->subjectTitle = $omnitrackerEnrolmentData->Studiengangsversion;
        $enrolmentData->subjectEcts = $omnitrackerEnrolmentData->StudiengangsversionReqEcts;
        $enrolmentData->combination = $omnitrackerEnrolmentData->StudienstrukturUniqueId;
        $enrolmentData->combinationTitle = $omnitrackerEnrolmentData->Studienstruktur;
        $enrolmentData->combinationEcts = $omnitrackerEnrolmentData->StudienstrukturReqEcts;
        $enrolmentData->studyProgram = $omnitrackerEnrolmentData->Studienprogramm;
        $enrolmentData->furtherInformation = $omnitrackerEnrolmentData->WunschEinstufungsSemester;
        $enrolmentData->studienstrukturParallelUniqueId = $omnitrackerEnrolmentData->StudienstrukturParallelUniqueId;
        $enrolmentData->studienstufeBFS = $omnitrackerEnrolmentData->StudienstufeBFS;
        $enrolmentData->degreeProgram = $omnitrackerEnrolmentData->StudienstufeUniqueId;
        $enrolmentData->homePhoneAreaCode = $omnitrackerEnrolmentData->Telefon;
        $enrolmentData->homePhoneNumber = $omnitrackerEnrolmentData->TelefonTyp;
        $enrolmentData->vorbildungMasterabschluss = $omnitrackerEnrolmentData->VorbildungMasterabschluss;
        $enrolmentData->firstName = $omnitrackerEnrolmentData->Vorname;

        return $enrolmentData;
    }

    public function mapUnibeEnrolmentOmnitrackerEnrolment(FluxEco\UnibeEnrolment\StateDataEnrolment|stdClass $enrolmentData): UnibeEnrolment\StateDataEnrolment|stdClass
    {
        /**
         * @var FluxEco\UnibeEnrolment\StateDataEnrolment|stdClass $omnitrackerEnrolmentData
         */
        $omnitrackerEnrolmentData = new stdClass();
        $omnitrackerEnrolmentData->Identifikationsnummer = $enrolmentData->identificationNumber;
        $omnitrackerEnrolmentData->AHV = $enrolmentData->oldAgeSurvivorInsuranceNumber;
        $omnitrackerEnrolmentData->Anrede = $enrolmentData->salutation;
        $omnitrackerEnrolmentData->ElternAdresszusatz = $enrolmentData->parentsAddressExtraAddressLine;
        $omnitrackerEnrolmentData->ElternAnrede = $enrolmentData->parentsAddressSalutation;
        $omnitrackerEnrolmentData->ElternLandUniqueId = $enrolmentData->parentsAddressCountry;
        $omnitrackerEnrolmentData->ElternNachname = $enrolmentData->parentsAddressLastName;
        $omnitrackerEnrolmentData->ElternOrtUniqueId = $enrolmentData->parentsAddressPlace;
        $omnitrackerEnrolmentData->ElternPLZ = $enrolmentData->parentsAddressPostalCode;
        $omnitrackerEnrolmentData->ElternStrasse = $enrolmentData->parentsAddressStreet;
        $omnitrackerEnrolmentData->ElternVorname = $enrolmentData->parentsAddressFirstNames;
        $omnitrackerEnrolmentData->EmailPrivat = $enrolmentData->email;
        $omnitrackerEnrolmentData->Geburtstag = $enrolmentData->birthDate;
        //$omnitrackerEnrolmentData->GeneralNotes = $enrolmentData->GeneralNotes;
        $omnitrackerEnrolmentData->HeimatortUniqueId = $enrolmentData->originPlace;
        $omnitrackerEnrolmentData->KorrespondenzspracheUniqueId = $enrolmentData->correspondenceLanguage;
        $omnitrackerEnrolmentData->LandUniqueId = $enrolmentData->country;
        $omnitrackerEnrolmentData->LastCompletedController = $enrolmentData->lastCompletedController;
        $omnitrackerEnrolmentData->MobilitaetHeimuniUniqueId = $enrolmentData->mobilitaetHeimuniUniqueId;
        $omnitrackerEnrolmentData->MutterspracheUniqueId = $enrolmentData->motherLanguage;
        $omnitrackerEnrolmentData->Nachname = $enrolmentData->lastName;
        $omnitrackerEnrolmentData->NationalitaetUniqueId = $enrolmentData->nationally;
        $omnitrackerEnrolmentData->Parallelstudium = $enrolmentData->parallelstudium;
        $omnitrackerEnrolmentData->PostEltern = $enrolmentData->parentsAddressGeneralPost;
        $omnitrackerEnrolmentData->RechnungEltern = $enrolmentData->parentsAddressInvoices;
        $omnitrackerEnrolmentData->SemesterUniqueId = $enrolmentData->semester;
        $omnitrackerEnrolmentData->StudentAdresszusatz = $enrolmentData->extraAddressLine;
        $omnitrackerEnrolmentData->StudentOrtUniqueId = $enrolmentData->place;
        $omnitrackerEnrolmentData->StudentPLZ = $enrolmentData->postalCode;
        $omnitrackerEnrolmentData->StudentPostfach = $enrolmentData->postalOfficeBox;
        $omnitrackerEnrolmentData->StudentStrasse = $enrolmentData->street;
        $omnitrackerEnrolmentData->StudiengangsversionParallelUniqueId = $enrolmentData->studiengangsversionParallelUniqueId;
        $omnitrackerEnrolmentData->StudiengangsversionUniqueId = $enrolmentData->subject;
        $omnitrackerEnrolmentData->StudienstrukturParallelUniqueId = $enrolmentData->studienstrukturParallelUniqueId;
        $omnitrackerEnrolmentData->StudienstrukturUniqueId = $enrolmentData->combination;
        $omnitrackerEnrolmentData->Studienprogramm = $enrolmentData->studyProgram;
        $omnitrackerEnrolmentData->WunschEinstufungsSemester = $enrolmentData->furtherInformation;
        $omnitrackerEnrolmentData->StudienstufeBFS = $enrolmentData->studienstufeBFS;
        $omnitrackerEnrolmentData->StudienstufeUniqueId = $enrolmentData->degreeProgram;
        $omnitrackerEnrolmentData->Telefon = $enrolmentData->homePhoneAreaCode;
        $omnitrackerEnrolmentData->TelefonTyp = $enrolmentData->homePhoneNumber;
        $omnitrackerEnrolmentData->VorbildungMasterabschluss = $enrolmentData->vorbildungMasterabschluss;
        $omnitrackerEnrolmentData->Vorname = $enrolmentData->firstName;

        return $omnitrackerEnrolmentData;
    }

    /**
     * @throws Exception
     */
    private function jsonFileProcessorApi(): JsonFileProcessor\Api
    {
        return JsonFileProcessor\Api::new([1985]);
    }

    private function unibeEnrolmentApi(string $pageDataDirectory, string $inputOptionDataDirectory, object $inputNames): UnibeEnrolment\Api
    {
        $unibeEnrolmentState = UnibeEnrolment\State::newEmpty($pageDataDirectory, $inputOptionDataDirectory, $inputNames, $this->objectFromJsonFile());
        return UnibeEnrolment\Api::new($unibeEnrolmentState);
    }

    private function httpTransactionGateway(
        string $layoutDataDirectory,
        string $layoutFileName
    ): HttpWorkflowRequestHandler\Api
    {
        $httpTransactionGatewayState = HttpWorkflowRequestHandler\State::newEmpty(
            $layoutDataDirectory,
            $layoutFileName
        );
        return HttpWorkflowRequestHandler\Api::new($httpTransactionGatewayState);
    }

    private function omnitrackerClientApi(): UnibeOmnitrackerClient\Api
    {
        return UnibeOmnitrackerClient\Api::new();
    }

    private function toCamelCase(string $name)
    {
        $str = str_replace('-', ' ', $name);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);
        return lcfirst($str);
    }
}