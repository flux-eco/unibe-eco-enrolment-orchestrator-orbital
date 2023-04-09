<?php
require_once __DIR__ . "/autoload.php";
$dataDirectory = __DIR__ . "/data/configs";

$objectMapper = FluxEco\ObjectMapper\Api::new();
$fileProcessor = FluxEco\JsonFileProcessor\Api::new();

$requestHandler = Api::new()->unibeEnrolmentApi;

$unibeEnrolmentApi = FluxEco\UnibeEnrolment\Api::new(
    FluxEco\UnibeEnrolment\Types\Outbounds::new(
        Adapters::new()->newUnibeEnrolmentOutboundsActionsProcessor()
    )
);

$omnitrackerClient = FluxEco\UnibeOmnitrackerClient\Api::new();

$salutationInputOptionStateFile = $unibeEnrolmentApi->readEnrolmentDefinition()->inputOptions->salutations->stateFilePath;
try {
    $fileProcessor->writeJsonFile(
        implode("/", [$salutationInputOptionStateFile->directoryPath, $salutationInputOptionStateFile->fileName]),
        $omnitrackerClient->readSalutations()
    );
} catch (Exception $e) {
}
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
