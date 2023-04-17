<?php

use FluxEcoType\FluxEcoAttributeDefinition;
use libphonenumber\PhoneNumberUtil;

require_once __DIR__ . "/autoload.php";
$dataDirectory = __DIR__ . "/data/configs";
$config = Config::new();
$objectMapper = FluxEco\ObjectMapper\Api::new();
$fileProcessor = FluxEco\JsonFileProcessor\Api::new([$config->serverSystemUserId]);
$requestHandler = Api::new()->unibeEnrolmentApi;
$unibeEnrolmentApi = FluxEco\UnibeEnrolment\Api::new(FluxEco\UnibeEnrolment\Types\Outbounds::new(Adapters::new()->newUnibeEnrolmentOutboundsActionsProcessor()));
$enrolmentDefinition = $unibeEnrolmentApi->readEnrolmentDefinition();
$omnitrackerClient = FluxEco\UnibeOmnitrackerClient\Api::new();

$writeJsonFile = function (FluxEcoType\FluxEcoFilePathDefinition $filePathDefinition, array|object $data, array $mapping = ["id" => "id", "label" => "label"]) use ($fileProcessor, $objectMapper) {
    $fileProcessor->writeJsonFile(
        implode("/", [$filePathDefinition->directoryPath, $filePathDefinition->fileName]),
        count($mapping) ? $objectMapper->createMappedObjectList($data, $mapping) : $data
    );
};

$readPhoneNumberCountryCodes = function() {
    $phoneNumberUtil = PhoneNumberUtil::getInstance(PhoneNumberUtil::META_DATA_FILE_PREFIX);
    $idLabelList = [];
    foreach($phoneNumberUtil->getSupportedCallingCodes() as $callingCode) {
        $idLabelObject = new stdClass();
        $idLabelObject->id = $callingCode;
        $idLabelObject->label = "+".$callingCode;
        $idLabelList[] = $idLabelObject;
    }
    return $idLabelList;
};

$writeJsonFile($enrolmentDefinition->inputOptions->salutations->stateFilePath, $omnitrackerClient->readSalutations(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->semesters->stateFilePath, $omnitrackerClient->readSemesters(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->subjects->stateFilePath, $omnitrackerClient->readSubjects(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->subjectCombinations->stateFilePath, $omnitrackerClient->readSubjectCombinations(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->originPlaces->stateFilePath, $omnitrackerClient->readOriginPlaces(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->places->stateFilePath, $omnitrackerClient->readPlaces(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->motherLanguages->stateFilePath, $omnitrackerClient->readMotherLanguage(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->correspondenceLanguages->stateFilePath, $omnitrackerClient->readCorrespondenceLanguage(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->countries->stateFilePath, $omnitrackerClient->readCountries(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->areaCodes->stateFilePath, $readPhoneNumberCountryCodes(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->nationalities->stateFilePath, $omnitrackerClient->readNationalities(), []);



/*
$fileProcessor->writeJsonFile(
    $requestHandler->readSettings()->enrolmentInputOptionsRepositoryActionDefinitions->readMunicipalities->path, $objectMapper->createMappedObjectList($omnitrackerClient->readMunicipalities(), ["id" => "id", "label" => "label"])
);
$fileProcessor->writeJsonFile(
    $requestHandler->readSettings()->enrolmentInputOptionsRepositoryActionDefinitions->readCantons->path, $objectMapper->createMappedObjectList($omnitrackerClient->readCantons(), ["id" => "id", "label" => "label"])
);
$fileProcessor->writeJsonFile(
    $requestHandler->readSettings()->enrolmentInputOptionsRepositoryActionDefinitions->readCountries->path, $objectMapper->createMappedObjectList($omnitrackerClient->readCountries(), ["id" => "id", "label" => "label"])
);
$fileProcessor->writeJsonFile(
    $requestHandler->readSettings()->enrolmentInputOptionsRepositoryActionDefinitions->readCertificates->path, $objectMapper->createMappedObjectList($omnitrackerClient->readCertificates(), ["id" => "id", "label" => "label"])
);
*/
