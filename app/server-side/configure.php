<?php
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

/*
$writeJsonFile($enrolmentDefinition->inputOptions->salutations->stateFilePath, $omnitrackerClient->readSalutations());
$writeJsonFile($enrolmentDefinition->inputOptions->semesters->stateFilePath, $omnitrackerClient->readSemesters());
$writeJsonFile($enrolmentDefinition->inputOptions->subjects->stateFilePath, $omnitrackerClient->readSubjects(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->subjectCombinations->stateFilePath, $omnitrackerClient->readSubjectCombinations(), []);
*/
$writeJsonFile($enrolmentDefinition->inputOptions->places->stateFilePath, $omnitrackerClient->readPlaces(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->motherLanguage->stateFilePath, $omnitrackerClient->readMotherLanguage(), []);
$writeJsonFile($enrolmentDefinition->inputOptions->correspondenceLanguage->stateFilePath, $omnitrackerClient->readCorrespondenceLanguage(), []);




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
