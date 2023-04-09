<?php

use FluxEco\JsonFileProcessor;
use FluxEco\UnibeEnrolment;
use FluxEco\HttpWorkflowRequestHandler;
use FluxEco\UnibeEnrolment\Types\Enrolment\OutputDataObject;
use FluxEco\UnibeOmnitrackerClient;
use FluxEcoType\FluxEcoTransactionStateObject;
use FluxEco\ObjectMapper;

final readonly class Adapters
{
    private function __construct()
    {

    }

    public static function new()
    {
        return new self();
    }

    public function newHttpWorkflowRequestHandlerOutboundsActionsProcessor(UnibeEnrolment\Api $unibeEnrolmentApi): HttpWorkflowRequestHandler\Types\OutboundsActionsProcessor
    {
        return new class($unibeEnrolmentApi) implements HttpWorkflowRequestHandler\Types\OutboundsActionsProcessor {
            private UnibeEnrolment\Api $unibeEnrolmentApi;

            public function __construct(UnibeEnrolment\Api $enrolmentApi)
            {
                $this->unibeEnrolmentApi = $enrolmentApi;
            }

            public function processCreateTransactionId(): string
            {
                return FluxEcoType\FluxEcoId::newHashedUuid4()->id;
            }

            public function processReadCurrentPage(FluxEcoTransactionStateObject $transactionStateObject): string
            {
                return $this->unibeEnrolmentApi->readPage($transactionStateObject);
            }

            public function processReadLayout(): string
            {
                return $this->unibeEnrolmentApi->readLayout();
            }

            public function processStoreRequestContent(FluxEcoType\FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
            {
                return $this->unibeEnrolmentApi->storeData($transactionStateObject, $dataToProcess);
            }
        };
    }

    public function newUnibeEnrolmentOutboundsActionsProcessor(): UnibeEnrolment\Types\OutboundsActionsProcessor
    {

        return new class() implements UnibeEnrolment\Types\OutboundsActionsProcessor {
            private JsonFileProcessor\Api $jsonFileProcessor;
            private UnibeOmnitrackerClient\Api $unibeOmnitrackerClient;


            public function __construct()
            {
                $this->jsonFileProcessor = JsonFileProcessor\Api::new();
                $this->unibeOmnitrackerClient = UnibeOmnitrackerClient\Api::new();
            }

            public function processReadJsonFile(string $directoryPath, string $jsonFileName): string
            {
                return $this->jsonFileProcessor->readJsonFile($directoryPath, $jsonFileName);
            }

            public function processCreateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
            {
                //todo encrypt password
                return $this->unibeOmnitrackerClient->createEnrolment($transactionStateObject->transactionId, $dataToProcess->password);
            }

            public function processUpdateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess, OutputDataObject $dataToProcessAttributesDefinition): object
            {
                $itemAttributesDefinitionToMapTo = $this->unibeOmnitrackerClient->readBaseDataItemAttributesDefinition();
                $srcKeyToNewKeyMapping = [
                    $dataToProcessAttributesDefinition->ahv->name => $itemAttributesDefinitionToMapTo->ahv->name,
                    $dataToProcessAttributesDefinition->anrede->name => $itemAttributesDefinitionToMapTo->anrede->name,
                    $dataToProcessAttributesDefinition->anredeuniqueid->name => $itemAttributesDefinitionToMapTo->anredeuniqueid->name,
                    $dataToProcessAttributesDefinition->countrycode->name => $itemAttributesDefinitionToMapTo->countrycode->name,
                    $dataToProcessAttributesDefinition->elternadresszusatz->name => $itemAttributesDefinitionToMapTo->elternadresszusatz->name,
                    $dataToProcessAttributesDefinition->elternanrede->name => $itemAttributesDefinitionToMapTo->elternanrede->name,
                    $dataToProcessAttributesDefinition->elternanredeuniqueid->name => $itemAttributesDefinitionToMapTo->elternanredeuniqueid->name,
                    $dataToProcessAttributesDefinition->elternlanduniqueid->name => $itemAttributesDefinitionToMapTo->elternlanduniqueid->name,
                    $dataToProcessAttributesDefinition->elternnachname->name => $itemAttributesDefinitionToMapTo->elternnachname->name,
                    $dataToProcessAttributesDefinition->elternort->name => $itemAttributesDefinitionToMapTo->elternort->name,
                    $dataToProcessAttributesDefinition->elternortuniqueid->name => $itemAttributesDefinitionToMapTo->elternortuniqueid->name,
                    $dataToProcessAttributesDefinition->elternplz->name => $itemAttributesDefinitionToMapTo->elternplz->name,
                    $dataToProcessAttributesDefinition->elternpostfach->name => $itemAttributesDefinitionToMapTo->elternpostfach->name,
                    $dataToProcessAttributesDefinition->elternpostfachvorhanden->name => $itemAttributesDefinitionToMapTo->elternpostfachvorhanden->name,
                    $dataToProcessAttributesDefinition->elternstrasse->name => $itemAttributesDefinitionToMapTo->elternstrasse->name,
                    $dataToProcessAttributesDefinition->elternvorname->name => $itemAttributesDefinitionToMapTo->elternvorname->name,
                    $dataToProcessAttributesDefinition->emailprivat->name => $itemAttributesDefinitionToMapTo->emailprivat->name,
                    $dataToProcessAttributesDefinition->geburtstag->name => $itemAttributesDefinitionToMapTo->geburtstag->name,
                    $dataToProcessAttributesDefinition->generalnotes->name => $itemAttributesDefinitionToMapTo->generalnotes->name,
                    $dataToProcessAttributesDefinition->heimatortuniqueid->name => $itemAttributesDefinitionToMapTo->heimatortuniqueid->name,
                    $dataToProcessAttributesDefinition->identifikationsnummer->name => $itemAttributesDefinitionToMapTo->identifikationsnummer->name,
                    $dataToProcessAttributesDefinition->korrespondenzspraccheuniqueid->name => $itemAttributesDefinitionToMapTo->korrespondenzspraccheuniqueid->name,
                    $dataToProcessAttributesDefinition->land->name => $itemAttributesDefinitionToMapTo->land->name,
                    $dataToProcessAttributesDefinition->landuniqueid->name => $itemAttributesDefinitionToMapTo->landuniqueid->name,
                    $dataToProcessAttributesDefinition->lastcompletedcontroller->name => $itemAttributesDefinitionToMapTo->lastcompletedcontroller->name,
                    $dataToProcessAttributesDefinition->martikelnmmer->name => $itemAttributesDefinitionToMapTo->martikelnmmer->name,
                    $dataToProcessAttributesDefinition->mobilitaetheimuniuniqueid->name => $itemAttributesDefinitionToMapTo->mobilitaetheimuniuniqueid->name,
                    $dataToProcessAttributesDefinition->mutterspracheuniqueid->name => $itemAttributesDefinitionToMapTo->mutterspracheuniqueid->name,
                    $dataToProcessAttributesDefinition->nachname->name => $itemAttributesDefinitionToMapTo->nachname->name,
                    $dataToProcessAttributesDefinition->nationalitaetuniqueid->name => $itemAttributesDefinitionToMapTo->nationalitaetuniqueid->name,
                    $dataToProcessAttributesDefinition->parallelstudium->name => $itemAttributesDefinitionToMapTo->parallelstudium->name,
                    $dataToProcessAttributesDefinition->posteltern->name => $itemAttributesDefinitionToMapTo->posteltern->name,
                    $dataToProcessAttributesDefinition->poststudierend->name => $itemAttributesDefinitionToMapTo->poststudierend->name,
                    $dataToProcessAttributesDefinition->pruefingsmisserfolgmajor->name => $itemAttributesDefinitionToMapTo->pruefingsmisserfolgmajor->name,
                    $dataToProcessAttributesDefinition->qualificationstudiesatuniversityofbern->name => $itemAttributesDefinitionToMapTo->qualificationstudiesatuniversityofbern->name,
                    $dataToProcessAttributesDefinition->rechnungeltern->name => $itemAttributesDefinitionToMapTo->rechnungeltern->name,
                    $dataToProcessAttributesDefinition->rechnungstudierend->name => $itemAttributesDefinitionToMapTo->rechnungstudierend->name,
                    $dataToProcessAttributesDefinition->registrationcompleted->name => $itemAttributesDefinitionToMapTo->registrationcompleted->name,
                    $dataToProcessAttributesDefinition->semesteruniqueid->name => $itemAttributesDefinitionToMapTo->semesteruniqueid->name,
                    $dataToProcessAttributesDefinition->studentadresszusatz->name => $itemAttributesDefinitionToMapTo->studentadresszusatz->name,
                    $dataToProcessAttributesDefinition->studentort->name => $itemAttributesDefinitionToMapTo->studentort->name,
                    $dataToProcessAttributesDefinition->studentortuniqueid->name => $itemAttributesDefinitionToMapTo->studentortuniqueid->name,
                    $dataToProcessAttributesDefinition->studentplz->name => $itemAttributesDefinitionToMapTo->studentplz->name,
                    $dataToProcessAttributesDefinition->studentpostfach->name => $itemAttributesDefinitionToMapTo->studentpostfach->name,
                    $dataToProcessAttributesDefinition->studentpostfachvorhanden->name => $itemAttributesDefinitionToMapTo->studentpostfachvorhanden->name,
                    $dataToProcessAttributesDefinition->studentstrasse->name => $itemAttributesDefinitionToMapTo->studentstrasse->name,
                    $dataToProcessAttributesDefinition->studiengangsversion->name => $itemAttributesDefinitionToMapTo->studiengangsversion->name,
                    $dataToProcessAttributesDefinition->studiengangsversionparallel->name => $itemAttributesDefinitionToMapTo->studiengangsversionparallel->name,
                    $dataToProcessAttributesDefinition->studiengangsversionparallelreqects->name => $itemAttributesDefinitionToMapTo->studiengangsversionparallelreqects->name,
                    $dataToProcessAttributesDefinition->studiengangsversionparalleluniqueid->name => $itemAttributesDefinitionToMapTo->studiengangsversionparalleluniqueid->name,
                    $dataToProcessAttributesDefinition->studiengangsversionreqects->name => $itemAttributesDefinitionToMapTo->studiengangsversionreqects->name,
                    $dataToProcessAttributesDefinition->studiengangsversionuniqueid->name => $itemAttributesDefinitionToMapTo->studiengangsversionuniqueid->name,
                    $dataToProcessAttributesDefinition->studienstruktur->name => $itemAttributesDefinitionToMapTo->studienstruktur->name,
                    $dataToProcessAttributesDefinition->studienstrukturparallel->name => $itemAttributesDefinitionToMapTo->studienstrukturparallel->name,
                    $dataToProcessAttributesDefinition->studienstrukturparallelreqects->name => $itemAttributesDefinitionToMapTo->studienstrukturparallelreqects->name,
                    $dataToProcessAttributesDefinition->studienstrukturparalleluniqueid->name => $itemAttributesDefinitionToMapTo->studienstrukturparalleluniqueid->name,
                    $dataToProcessAttributesDefinition->studienstrukturreqects->name => $itemAttributesDefinitionToMapTo->studienstrukturreqects->name,
                    $dataToProcessAttributesDefinition->studienstrukturuniqueid->name => $itemAttributesDefinitionToMapTo->studienstrukturuniqueid->name,
                    $dataToProcessAttributesDefinition->studienstufebfs->name => $itemAttributesDefinitionToMapTo->studienstufebfs->name,
                    $dataToProcessAttributesDefinition->studienstufeuniqueid->name => $itemAttributesDefinitionToMapTo->studienstufeuniqueid->name,
                    $dataToProcessAttributesDefinition->studierendenkategorieuniqueid->name => $itemAttributesDefinitionToMapTo->studierendenkategorieuniqueid->name,
                    $dataToProcessAttributesDefinition->telefon->name => $itemAttributesDefinitionToMapTo->telefon->name,
                    $dataToProcessAttributesDefinition->telefontyp->name => $itemAttributesDefinitionToMapTo->telefontyp->name,
                    $dataToProcessAttributesDefinition->uniqueid->name => $itemAttributesDefinitionToMapTo->uniqueid->name,
                    $dataToProcessAttributesDefinition->vorbildungmasterabschluss->name => $itemAttributesDefinitionToMapTo->vorbildungmasterabschluss->name,
                    $dataToProcessAttributesDefinition->vorname->name => $itemAttributesDefinitionToMapTo->vorname->name,
                    $dataToProcessAttributesDefinition->vorname2->name => $itemAttributesDefinitionToMapTo->vorname2->name,
                    $dataToProcessAttributesDefinition->vorname3->name => $itemAttributesDefinitionToMapTo->vorname3->name,
                    $dataToProcessAttributesDefinition->wunscheinstufungssemester->name => $itemAttributesDefinitionToMapTo->wunscheinstufungssemester->name,
                ];


                $baseDataItem = ObjectMapper\Api::new()->createMappedObject(
                    $dataToProcess,
                    $srcKeyToNewKeyMapping,
                    ObjectMapper\Types\MappingOptions::new(
                        true,
                        ObjectMapper\Types\MappingOptionSetStateAttributeValueIfSourceAttributeNotExists::new($transactionStateObject->data)
                    )
                );
                return $this->unibeOmnitrackerClient->updateEnrolment($transactionStateObject->transactionId, $baseDataItem);
            }
        };
    }
}