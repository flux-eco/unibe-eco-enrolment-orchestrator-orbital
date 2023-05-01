<?php

use FluxEco\UnibeEnrolment;
use FluxEco\JsonFileProcessor;
use FluxEco\HttpWorkflowRequestHandler;
use FluxEco\UnibeOmnitrackerClient;
use FluxEcoType\FluxEcoStateValues;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

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

            /**
             * todo if debug
             */
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
        //$enrolmentData->GeneralNotes = $omnitrackerEnrolmentData->GeneralNotes;
        $enrolmentData->lastCompletedController = $omnitrackerEnrolmentData->LastCompletedController;
        $enrolmentData->mobilitaetHeimuniUniqueId = $omnitrackerEnrolmentData->MobilitaetHeimuniUniqueId;
        $enrolmentData->parallelstudium = $omnitrackerEnrolmentData->Parallelstudium;
        $enrolmentData->semester = $omnitrackerEnrolmentData->SemesterUniqueId;
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
        $enrolmentData->vorbildungMasterabschluss = $omnitrackerEnrolmentData->VorbildungMasterabschluss;

        /**
         * Personal Data
         */
        $enrolmentData->salutation = $omnitrackerEnrolmentData->AnredeUniqueId;
        $enrolmentData->firstName = $omnitrackerEnrolmentData->Vorname;
        $enrolmentData->secondFirstName = $omnitrackerEnrolmentData->Vorname2;
        $enrolmentData->additionalFirstNames = $this->mapCommaSeparatedStringToArray($omnitrackerEnrolmentData->Vorname3);
        $enrolmentData->lastName = $omnitrackerEnrolmentData->Nachname;
        $enrolmentData->registrationNumber = $omnitrackerEnrolmentData->Matrikelnummer;
        $enrolmentData->country = $omnitrackerEnrolmentData->LandUniqueId;
        $enrolmentData->extraAddressLine = $omnitrackerEnrolmentData->StudentAdresszusatz;
        $enrolmentData->street = $this->extractStringFromStringSpaceNumberString($omnitrackerEnrolmentData->StudentStrasse);
        $enrolmentData->houseNumber = $this->extractNumberFromStringSpaceNumberString($omnitrackerEnrolmentData->StudentStrasse);
        $enrolmentData->postalOfficeBox = $omnitrackerEnrolmentData->StudentPostfach;
        $enrolmentData->postalCode = $omnitrackerEnrolmentData->StudentPLZ;
        $enrolmentData->place = $omnitrackerEnrolmentData->StudentOrtUniqueId;
        $enrolmentData->businessPhoneAreaCode = $this->mapPhoneNumberToAreaCode($omnitrackerEnrolmentData->Telefon, $omnitrackerEnrolmentData->TelefonTyp, "business");
        $enrolmentData->businessPhoneNumber = $this->removeAreaCodeFromPhoneNumber($omnitrackerEnrolmentData->Telefon, $omnitrackerEnrolmentData->TelefonTyp, "business");
        $enrolmentData->homePhoneAreaCode = $this->mapPhoneNumberToAreaCode($omnitrackerEnrolmentData->Telefon, $omnitrackerEnrolmentData->TelefonTyp, "home");
        $enrolmentData->homePhoneNumber = $this->removeAreaCodeFromPhoneNumber($omnitrackerEnrolmentData->Telefon, $omnitrackerEnrolmentData->TelefonTyp, "home");
        $enrolmentData->mobilePhoneAreaCode = $this->mapPhoneNumberToAreaCode($omnitrackerEnrolmentData->Telefon, $omnitrackerEnrolmentData->TelefonTyp, "mobile");
        $enrolmentData->mobilePhoneNumber = $this->removeAreaCodeFromPhoneNumber($omnitrackerEnrolmentData->Telefon, $omnitrackerEnrolmentData->TelefonTyp, "mobile");
        $enrolmentData->email = $omnitrackerEnrolmentData->EmailPrivat;
        $enrolmentData->motherLanguage = $omnitrackerEnrolmentData->MutterspracheUniqueId;
        $enrolmentData->correspondenceLanguage = $omnitrackerEnrolmentData->KorrespondenzspracheUniqueId;
        $enrolmentData->birthDate = $omnitrackerEnrolmentData->Geburtstag;
        $enrolmentData->oldAgeSurvivarInsuranceNumber = $omnitrackerEnrolmentData->AHV; //todo check
        $enrolmentData->nationally = $omnitrackerEnrolmentData->NationalitaetUniqueId;
        $enrolmentData->originPlace = $omnitrackerEnrolmentData->HeimatortUniqueId;
        //parents-adress true?
        $enrolmentData->parentsAddressSalutation = $omnitrackerEnrolmentData->ElternAnredeUniqueId;
        $enrolmentData->parentsAddressFirstNames = $this->mapCommaSeparatedStringToArray($omnitrackerEnrolmentData->ElternVorname);
        $enrolmentData->parentsAddressLastName = $omnitrackerEnrolmentData->ElternNachname;
        //"parents-address-same-address": false ?
        $enrolmentData->parentsAddressCountry = $omnitrackerEnrolmentData->ElternLandUniqueId;
        $enrolmentData->parentsAddressExtraAddressLine = $omnitrackerEnrolmentData->ElternAdresszusatz;
        $enrolmentData->parentsAddressStreet = $this->extractStringFromStringSpaceNumberString($omnitrackerEnrolmentData->ElternStrasse);
        $enrolmentData->parentsAddressHouseNumber = $this->extractNumberFromStringSpaceNumberString($omnitrackerEnrolmentData->ElternStrasse);
        $enrolmentData->parentsAddressPostalCode = $omnitrackerEnrolmentData->ElternPLZ;
        $enrolmentData->parentsAddressPlace = $omnitrackerEnrolmentData->ElternOrtUniqueId;
        $enrolmentData->parentsAddressGeneralPost = (bool)$omnitrackerEnrolmentData->PostEltern;
        $enrolmentData->parentsAddressInvoices = (bool)$omnitrackerEnrolmentData->RechnungEltern;

        return $enrolmentData;
    }

    /**
     * @throws NumberParseException
     * @throws Exception
     */
    public function mapUnibeEnrolmentOmnitrackerEnrolment(FluxEco\UnibeEnrolment\StateDataEnrolment|stdClass $enrolmentData): UnibeEnrolment\StateDataEnrolment|stdClass
    {
        /**
         * @var FluxEco\UnibeOmnitrackerClient\StateDataEnrolment|stdClass $omnitrackerEnrolmentData
         */
        $omnitrackerEnrolmentData = new stdClass();
        $omnitrackerEnrolmentData->Identifikationsnummer = $enrolmentData->identificationNumber;
        //$omnitrackerEnrolmentData->GeneralNotes = $enrolmentData->GeneralNotes;
        $omnitrackerEnrolmentData->LastCompletedController = $enrolmentData->lastCompletedController;
        $omnitrackerEnrolmentData->MobilitaetHeimuniUniqueId = $enrolmentData->mobilitaetHeimuniUniqueId;
        $omnitrackerEnrolmentData->Parallelstudium = $enrolmentData->parallelstudium;
        $omnitrackerEnrolmentData->SemesterUniqueId = $enrolmentData->semester;
        $omnitrackerEnrolmentData->StudiengangsversionParallelUniqueId = $enrolmentData->studiengangsversionParallelUniqueId;
        $omnitrackerEnrolmentData->StudiengangsversionUniqueId = $enrolmentData->subject;
        $omnitrackerEnrolmentData->StudienstrukturParallelUniqueId = $enrolmentData->studienstrukturParallelUniqueId;
        $omnitrackerEnrolmentData->StudienstrukturUniqueId = $enrolmentData->combination;
        $omnitrackerEnrolmentData->Studienprogramm = $enrolmentData->studyProgram;
        $omnitrackerEnrolmentData->WunschEinstufungsSemester = $enrolmentData->furtherInformation;
        $omnitrackerEnrolmentData->StudienstufeBFS = $enrolmentData->studienstufeBFS;
        $omnitrackerEnrolmentData->StudienstufeUniqueId = $enrolmentData->degreeProgram;
        $omnitrackerEnrolmentData->VorbildungMasterabschluss = $enrolmentData->vorbildungMasterabschluss;

        /**
         * Personal Data
         */
        $omnitrackerEnrolmentData->AnredeUniqueId = $enrolmentData->salutation;
        $omnitrackerEnrolmentData->Vorname = $enrolmentData->firstName;
        $omnitrackerEnrolmentData->Vorname2 = $enrolmentData->secondFirstName;
        $omnitrackerEnrolmentData->Vorname3 = $this->mapArrayToCommaSeparatedString($enrolmentData->additionalFirstNames);
        $omnitrackerEnrolmentData->Nachname = $enrolmentData->lastName;
        $omnitrackerEnrolmentData->Matrikelnummer = $enrolmentData->registrationNumber;
        $omnitrackerEnrolmentData->LandUniqueId = $enrolmentData->country;
        $omnitrackerEnrolmentData->StudentAdresszusatz = $enrolmentData->extraAddressLine;
        $omnitrackerEnrolmentData->StudentStrasse = $this->mapArrayToSpaceSeparatedString([$enrolmentData->street, $enrolmentData->houseNumber]);
        $omnitrackerEnrolmentData->StudentPostfach = $enrolmentData->postalOfficeBox;
        $omnitrackerEnrolmentData->StudentPLZ = $enrolmentData->postalCode;
        $omnitrackerEnrolmentData->StudentOrtUniqueId = $enrolmentData->place;
        $omnitrackerEnrolmentData->Telefon = $this->mapPhoneNumber(
            $enrolmentData->businessPhoneAreaCode,
            $enrolmentData->businessPhoneNumber,
            $enrolmentData->homePhoneAreaCode,
            $enrolmentData->homePhoneNumber,
            $enrolmentData->mobilePhoneAreaCode,
            $enrolmentData->mobilePhoneNumber,
        );
        $omnitrackerEnrolmentData->TelefonTyp = $this->mapPhoneNumberType(
            $enrolmentData->businessPhoneAreaCode,
            $enrolmentData->businessPhoneNumber,
            $enrolmentData->homePhoneAreaCode,
            $enrolmentData->homePhoneNumber,
            $enrolmentData->mobilePhoneAreaCode,
            $enrolmentData->mobilePhoneNumber,
        );
        $omnitrackerEnrolmentData->EmailPrivat = $enrolmentData->email;
        $omnitrackerEnrolmentData->MutterspracheUniqueId = $enrolmentData->motherLanguage;
        $omnitrackerEnrolmentData->KorrespondenzspracheUniqueId = $enrolmentData->correspondenceLanguage;
        $omnitrackerEnrolmentData->Geburtstag = $enrolmentData->birthDate;
        $omnitrackerEnrolmentData->AHV = $enrolmentData->oldAgeSurvivarInsuranceNumber;
        $omnitrackerEnrolmentData->NationalitaetUniqueId = $enrolmentData->nationally;
        $omnitrackerEnrolmentData->HeimatortUniqueId = $enrolmentData->originPlace;
        //parents-adress true?
        $omnitrackerEnrolmentData->ElternAnredeUniqueId = $enrolmentData->parentsAddressSalutation;
        $omnitrackerEnrolmentData->ElternVorname = $this->mapArrayToCommaSeparatedString($enrolmentData->parentsAddressFirstNames);
        $omnitrackerEnrolmentData->ElternNachname = $enrolmentData->parentsAddressLastName;
        //"parents-address-same-address": false ?
        $omnitrackerEnrolmentData->ElternLandUniqueId = $enrolmentData->parentsAddressCountry;
        $omnitrackerEnrolmentData->ElternAdresszusatz = $enrolmentData->parentsAddressExtraAddressLine;
        $omnitrackerEnrolmentData->ElternStrasse = $this->mapArrayToSpaceSeparatedString([$enrolmentData->parentsAddressStreet, $enrolmentData->parentsAddressHouseNumber]);
        $omnitrackerEnrolmentData->ElternPLZ = $enrolmentData->parentsAddressPostalCode;
        $omnitrackerEnrolmentData->ElternOrtUniqueId = $enrolmentData->parentsAddressPlace;
        $omnitrackerEnrolmentData->PostEltern = (int)$enrolmentData->parentsAddressGeneralPost;
        $omnitrackerEnrolmentData->RechnungEltern = (int)$enrolmentData->parentsAddressInvoices;


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

    private function mapArrayToCommaSeparatedString($inputArray): string
    {
        return implode(", ", $inputArray);
    }

    private function mapCommaSeparatedStringToArray(string $inputString): array
    {
        return explode(", ", $inputString);
    }

    private function mapArrayToSpaceSeparatedString(array $inputArray): string
    {
        return trim(implode(" ", $inputArray));
    }

    private function extractStringFromStringSpaceNumberString(string $inputString): string
    {
        $elements = explode(' ', $inputString);
        $lastElement = end($elements);
        $lastElement = trim($lastElement);

        if (is_numeric($lastElement)) {
            return trim(str_replace($lastElement, '', $inputString));
        } else {
            return $inputString;
        }
    }

    private function extractNumberFromStringSpaceNumberString(string $inputString): int|null
    {
        $elements = explode(' ', $inputString);
        $lastElement = end($elements);
        $lastElement = trim($lastElement);

        if (is_numeric($lastElement)) {
            return $lastElement;
        } else {
            return null;
        }
    }


    /**
     * - Maps the processed phone numbers to the phone number with the highest priority
     *   The frontend already validates, that only one phone number is valid by processing the form
     * - Validates the phone number with googles PhoneNumberUtil
     * @throws NumberParseException
     */
    public function mapPhoneNumber(
        string $businessPhoneAreaCode,
        string $businessPhoneNumber,
        string $homePhoneAreaCode,
        string $homePhoneNumber,
        string $mobilePhoneAreaCode,
        string $mobilePhoneNumber,
    ): string
    {
        $extractAndFormatNumber = function ($countryCode, $number) {
            $phoneNumberUtil = PhoneNumberUtil::getInstance(PhoneNumberUtil::META_DATA_FILE_PREFIX);
            $regionCode = $phoneNumberUtil->getRegionCodeForCountryCode($countryCode);
            $number = $phoneNumberUtil->parse($number, $regionCode);
            return $phoneNumberUtil->format($number, PhoneNumberFormat::INTERNATIONAL);
        };
        $phoneNumber = "";
        if (empty($businessPhoneAreaCode) === false && empty($businessPhoneNumber) === false) {
            $phoneNumber = $extractAndFormatNumber($businessPhoneAreaCode, $businessPhoneNumber);
        }
        if (empty($homePhoneAreaCode) === false && empty($homePhoneNumber) === false) {
            $phoneNumber = $extractAndFormatNumber($homePhoneAreaCode, $homePhoneNumber);
        }
        if (empty($mobilePhoneAreaCode) === false && empty($mobilePhoneNumber) === false) {
            $phoneNumber = $extractAndFormatNumber($mobilePhoneAreaCode, $mobilePhoneNumber);
        }
        return $phoneNumber;
    }

    /**
     * - Maps the processed phone numbers to the phone number type the highest priority
     *   The frontend already validates, that only one phone number is valid by processing the form
     * @throws Exception
     * @see mapPhoneNumber
     */
    public function mapPhoneNumberType(
        string $businessPhoneAreaCode,
        string $businessPhoneNumber,
        string $homePhoneAreaCode,
        string $homePhoneNumber,
        string $mobilePhoneAreaCode,
        string $mobilePhoneNumber,
    ): string
    {
        if (empty($businessPhoneAreaCode) === false && empty($businessPhoneNumber) === false) {
            return "business";
        }
        if (empty($homePhoneAreaCode) === false && empty($homePhoneNumber) === false) {
            return "home";
        }
        if (empty($mobilePhoneAreaCode) === false && empty($mobilePhoneNumber) === false) {
            return "mobile";
        }
        return "";
    }

    public function mapPhoneNumberToAreaCode(string $fromPhoneNumber, string $fromPhoneNumberType, string $toPhoneNumberType): string
    {
        if ($fromPhoneNumberType !== $toPhoneNumberType) {
            return "";
        }
        return explode(" ", $fromPhoneNumber)[0];
    }

    public function removeAreaCodeFromPhoneNumber(string $fromPhoneNumber, string $fromPhoneNumberType, string $toPhoneNumberType): string
    {
        if ($fromPhoneNumberType !== $toPhoneNumberType) {
            return "";
        }
        return substr(strstr($fromPhoneNumber, ' '), 1);
    }
}