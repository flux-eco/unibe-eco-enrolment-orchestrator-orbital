<?php

use FluxEco\JsonFileProcessor;
use FluxEco\UnibeEnrolment;
use FluxEco\HttpWorkflowRequestHandler;
use FluxEco\UnibeEnrolment\Types\Enrolment\WorkflowOutputDefinition;
use FluxEco\UnibeOmnitrackerClient;
use FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi\BaseDataItemAttributesDefinition;
use FluxEcoType\FluxEcoTransactionStateObject;
use FluxEco\ObjectMapper;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

final readonly class Adapters
{
    private function __construct(
        public Config $config
    )
    {

    }

    public static function new(): Adapters
    {
        return new self(Config::new());
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

            public function processReadNextPageName(string $lastHandledPageName, FluxEcoTransactionStateObject $transactionStateObject): string
            {
                $workflow = $this->unibeEnrolmentApi->readEnrolmentDefinition()->workflow;
                return $workflow->getNextPageName($lastHandledPageName, $transactionStateObject);
            }


            public function processReadCurrentPage(FluxEcoTransactionStateObject $transactionStateObject): string
            {
                return $this->unibeEnrolmentApi->readPage($transactionStateObject);
            }

            public function processReadPreviousPageName(FluxEcoTransactionStateObject $transactionStateObject): string
            {
                $handledPageNames = $transactionStateObject->handledPageNamesCurrentWorkflow;
                return end($handledPageNames);
            }

            public function processReadLayout(): string
            {
                return $this->unibeEnrolmentApi->readLayout();
            }

            public function processStoreRequestContent(FluxEcoType\FluxEcoTransactionStateObject $transactionStateObject, object $processData): object
            {
                return $this->unibeEnrolmentApi->storeData($transactionStateObject, $processData);
            }

            public function processReadStartPageName(): string
            {
                return $this->unibeEnrolmentApi->readEnrolmentDefinition()->workflow->startPageName;
            }

            public function isLastPage(FluxEcoTransactionStateObject $transactionStateObject): bool
            {
                $workflow = $this->unibeEnrolmentApi->readEnrolmentDefinition()->workflow;
                return ($transactionStateObject->currentPageName === $workflow->lastPageName);
            }

            public function isResumePage(string $pageName): bool
            {
                $workflow = $this->unibeEnrolmentApi->readEnrolmentDefinition()->workflow;
                return ($pageName === $workflow->resumePageName);
            }

            public function isStartPage(string $pageName): bool
            {
                $workflow = $this->unibeEnrolmentApi->readEnrolmentDefinition()->workflow;
                return ($pageName === $workflow->startPageName);
            }

            public function processReadResumeEnrolmentData(string $transactionId, object $processData): object
            {
                return $this->unibeEnrolmentApi->readPreviousEnrolment($transactionId, $processData);
            }

            public function processReadLastHandledPageNameFromWorkflowState(object $workflowState): string
            {
                $workflow = $this->unibeEnrolmentApi->readEnrolmentDefinition()->workflow;
                return $workflowState->{$workflow->workflowStateLastHandledPageAttributeName};
            }
        };
    }

    public function newUnibeEnrolmentOutboundsActionsProcessor(): UnibeEnrolment\Types\OutboundsActionsProcessor
    {

        return new class($this->config) implements UnibeEnrolment\Types\OutboundsActionsProcessor {
            private JsonFileProcessor\Api $jsonFileProcessor;
            private UnibeOmnitrackerClient\Api $unibeOmnitrackerClient;


            public function __construct(Config $config)
            {
                $this->jsonFileProcessor = JsonFileProcessor\Api::new([$config->serverSystemUserId]);
                $this->unibeOmnitrackerClient = UnibeOmnitrackerClient\Api::new();
            }

            public function processReadJsonFile(string $directoryPath, string $jsonFileName): string
            {
                return $this->jsonFileProcessor->readJsonFile($directoryPath, $jsonFileName);
            }

            public function processCreateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, string $password): object
            {
                return $this->unibeOmnitrackerClient->createEnrolment($transactionStateObject->transactionId, $password);
            }

            public function processReadResumeEnrolmentData(string $transactionId, string $identificationNumber, string $password): object
            {
                return $this->unibeOmnitrackerClient->readPreviousEnrolment($transactionId, $identificationNumber, $password);

            }

            public function processUpdateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess, WorkflowOutputDefinition $dataToProcessAttributesDefinition): object
            {
                $this->validate($transactionStateObject->currentPageName, $dataToProcess);

                $itemAttributesDefinitionToMapTo = $this->unibeOmnitrackerClient->readBaseDataItemAttributesDefinition();
                $srcKeyToNewKeyMapping = [
                    $dataToProcessAttributesDefinition->oldAgeSurvivarInsuranceNumber->name => $itemAttributesDefinitionToMapTo->ahv,
                    $dataToProcessAttributesDefinition->salutation->name => $itemAttributesDefinitionToMapTo->anredeuniqueid,
                    $dataToProcessAttributesDefinition->parentsAddressExtraAddressLine->name => $itemAttributesDefinitionToMapTo->elternadresszusatz,
                    $dataToProcessAttributesDefinition->parentsAddressSalutation->name => $itemAttributesDefinitionToMapTo->elternanredeuniqueid,
                    $dataToProcessAttributesDefinition->parentsAddressCountry->name => $itemAttributesDefinitionToMapTo->elternlanduniqueid,
                    $dataToProcessAttributesDefinition->parentsAddressLastName->name => $itemAttributesDefinitionToMapTo->elternnachname,
                    $dataToProcessAttributesDefinition->parentsAddressPlace->name => $itemAttributesDefinitionToMapTo->elternortuniqueid,
                    $dataToProcessAttributesDefinition->parentsAddressPostalCode->name => $itemAttributesDefinitionToMapTo->elternplz,
                    //$itemAttributesDefinitionToMapTo->elternpostfach->name,
                    //$itemAttributesDefinitionToMapTo->elternpostfachvorhanden->name,
                    $dataToProcessAttributesDefinition->parentsAddressStreet->name => $itemAttributesDefinitionToMapTo->elternstrasse,
                    $dataToProcessAttributesDefinition->parentsAddressFirstNames->name => function (object $srcObject, object $objectToMapTo, object $stateObject = null) use ($dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo) {
                        return $this->mapArrayToString($srcObject, $objectToMapTo, $dataToProcessAttributesDefinition->parentsAddressFirstNames->name, $itemAttributesDefinitionToMapTo->elternvorname->name);
                    },
                    $dataToProcessAttributesDefinition->email->name => $itemAttributesDefinitionToMapTo->emailprivat,
                    $dataToProcessAttributesDefinition->birthDate->name => $itemAttributesDefinitionToMapTo->geburtstag,
                    $dataToProcessAttributesDefinition->generalnotes->name => $itemAttributesDefinitionToMapTo->generalnotes,
                    $dataToProcessAttributesDefinition->originPlace->name => $itemAttributesDefinitionToMapTo->heimatortuniqueid,
                    $dataToProcessAttributesDefinition->correspondenceLanguage->name => $itemAttributesDefinitionToMapTo->korrespondenzspraccheuniqueid,
                    $dataToProcessAttributesDefinition->country->name => $itemAttributesDefinitionToMapTo->landuniqueid,
                    $dataToProcessAttributesDefinition->registrationNumber->name => $itemAttributesDefinitionToMapTo->martikelnmmer,
                    //$dataToProcessAttributesDefinition->mobilitaetheimuniuniqueid->name => $itemAttributesDefinitionToMapTo->mobilitaetheimuniuniqueid->name,
                    $dataToProcessAttributesDefinition->motherLanguage->name => $itemAttributesDefinitionToMapTo->mutterspracheuniqueid,
                    $dataToProcessAttributesDefinition->lastName->name => $itemAttributesDefinitionToMapTo->nachname,
                    $dataToProcessAttributesDefinition->nationally->name => $itemAttributesDefinitionToMapTo->nationalitaetuniqueid,
                    //$dataToProcessAttributesDefinition->parallelstudium->name => $itemAttributesDefinitionToMapTo->parallelstudium->name,
                    $dataToProcessAttributesDefinition->parentsAddressGeneralPost->name => function (object $srcObject, object $objectToMapTo, object $stateObject = null) use ($dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo) {
                        $sendToParentsKey = $dataToProcessAttributesDefinition->parentsAddressGeneralPost->name;
                        if (property_exists($srcObject, $sendToParentsKey)) {
                            return $this->mapSendTo($srcObject->{$sendToParentsKey}, $objectToMapTo, $itemAttributesDefinitionToMapTo->posteltern->name, $itemAttributesDefinitionToMapTo->poststudierend->name);
                        }

                        return $objectToMapTo;
                    },
                    //$dataToProcessAttributesDefinition->pruefingsmisserfolgmajor->name => $itemAttributesDefinitionToMapTo->pruefingsmisserfolgmajor->name,
                    //$dataToProcessAttributesDefinition->qualificationstudiesatuniversityofbern->name => $itemAttributesDefinitionToMapTo->qualificationstudiesatuniversityofbern->name,
                    $dataToProcessAttributesDefinition->parentsAddressInvoices->name => function (object $srcObject, object $objectToMapTo, object $stateObject = null) use ($dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo) {
                        $sendToParentsKey = $dataToProcessAttributesDefinition->parentsAddressInvoices->name;
                        if (property_exists($srcObject, $sendToParentsKey)) {
                            return $this->mapSendTo($srcObject->{$sendToParentsKey}, $objectToMapTo, $itemAttributesDefinitionToMapTo->rechnungeltern->name, $itemAttributesDefinitionToMapTo->rechnungstudierend->name);
                        }
                        return $objectToMapTo;
                    },
                    $dataToProcessAttributesDefinition->semester->name => $itemAttributesDefinitionToMapTo->semesteruniqueid,
                    $dataToProcessAttributesDefinition->extraAddressLine->name => $itemAttributesDefinitionToMapTo->studentadresszusatz,
                    $dataToProcessAttributesDefinition->place->name => $itemAttributesDefinitionToMapTo->studentortuniqueid,
                    $dataToProcessAttributesDefinition->postalCode->name => $itemAttributesDefinitionToMapTo->studentplz,
                    $dataToProcessAttributesDefinition->postalOfficeBox->name => $itemAttributesDefinitionToMapTo->studentpostfach,
                    //$dataToProcessAttributesDefinition->studentpostfachvorhanden->name => $itemAttributesDefinitionToMapTo->studentpostfachvorhanden->name,
                    $dataToProcessAttributesDefinition->street->name => function (object $srcObject, object $objectToMapTo, object $stateObject = null) use ($dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo) {
                        if (property_exists($srcObject, $dataToProcessAttributesDefinition->street->name)
                        ) {
                            if (property_exists($srcObject, $dataToProcessAttributesDefinition->houseNumber->name)) {
                                $objectToMapTo->{$itemAttributesDefinitionToMapTo->studentstrasse->name} = implode(" ", [$srcObject->{$dataToProcessAttributesDefinition->street->name}, $srcObject->{$dataToProcessAttributesDefinition->houseNumber->name}]);
                                return $objectToMapTo;
                            }
                            $objectToMapTo->{$itemAttributesDefinitionToMapTo->studentstrasse->name} = $srcObject->{$dataToProcessAttributesDefinition->street->name};
                            return $objectToMapTo;
                        }
                        return $objectToMapTo;
                    },
                    $dataToProcessAttributesDefinition->degreeProgram->name => $itemAttributesDefinitionToMapTo->studienstufeuniqueid,
                    // $dataToProcessAttributesDefinition->studiengangsversionparalleluniqueid->name => $itemAttributesDefinitionToMapTo->studiengangsversionparalleluniqueid->name,
                    $dataToProcessAttributesDefinition->subject->name => $itemAttributesDefinitionToMapTo->studiengangsversionuniqueid,
                    //$dataToProcessAttributesDefinition->studienstrukturparalleluniqueid->name => $itemAttributesDefinitionToMapTo->studienstrukturparalleluniqueid->name,
                    $dataToProcessAttributesDefinition->combination->name => $itemAttributesDefinitionToMapTo->studienstrukturuniqueid,
                    //$dataToProcessAttributesDefinition->studienstufebfs->name => $itemAttributesDefinitionToMapTo->studienstufebfs->name,
                    //$dataToProcessAttributesDefinition->studierendenkategorieuniqueid->name => $itemAttributesDefinitionToMapTo->studierendenkategorieuniqueid->name,
                    $dataToProcessAttributesDefinition->homePhoneNumber->name => function (object $srcObject, object $objectToMapTo, object $stateObject = null) use ($dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo) {
                        return $this->mapPhoneNumber($srcObject, $objectToMapTo, $dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo);
                    },
                    $dataToProcessAttributesDefinition->mobilePhoneNumber->name => function (object $srcObject, object $objectToMapTo, object $stateObject = null) use ($dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo) {
                        return $this->mapPhoneNumber($srcObject, $objectToMapTo, $dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo);
                    },
                    $dataToProcessAttributesDefinition->businessPhoneNumber->name => function (object $srcObject, object $objectToMapTo, object $stateObject = null) use ($dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo) {
                        return $this->mapPhoneNumber($srcObject, $objectToMapTo, $dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo);
                    },
                    //$dataToProcessAttributesDefinition->vorbildungmasterabschluss->name => $itemAttributesDefinitionToMapTo->vorbildungmasterabschluss->name,
                    $dataToProcessAttributesDefinition->firstName->name => $itemAttributesDefinitionToMapTo->vorname,
                    $dataToProcessAttributesDefinition->secondFirstName->name => $itemAttributesDefinitionToMapTo->vorname2,
                    $dataToProcessAttributesDefinition->additionalFirstNames->name => function (object $srcObject, object $objectToMapTo, object $stateObject = null) use ($dataToProcessAttributesDefinition, $itemAttributesDefinitionToMapTo) {
                        return $this->mapArrayToString($srcObject, $objectToMapTo, $dataToProcessAttributesDefinition->additionalFirstNames->name, $dataToProcessAttributesDefinition->additionalFirstNames->name);
                    },
                    //$dataToProcessAttributesDefinition->wunscheinstufungssemester->name => $itemAttributesDefinitionToMapTo->wunscheinstufungssemester->name,
                    $dataToProcessAttributesDefinition->registrationNumber->name => $itemAttributesDefinitionToMapTo->wunscheinstufungssemester,
                ];

                $baseDataItem = ObjectMapper\Api::new()->createMappedObject(
                    $dataToProcess,
                    $srcKeyToNewKeyMapping,
                    ObjectMapper\Types\MappingOptions::new(
                        true,
                        ObjectMapper\Types\MappingOptionPrefillFromCurrentState::new($transactionStateObject->data)
                    )
                );

                $baseDataItem->{$itemAttributesDefinitionToMapTo->lastcompletedcontroller->name} = $transactionStateObject->currentPageName;
                //todo if completed
                //$dataToProcessAttributesDefinition->registrationcompleted->name => $itemAttributesDefinitionToMapTo->registrationcompleted->name,

                return $this->unibeOmnitrackerClient->updateEnrolment($transactionStateObject->transactionId, $baseDataItem);
            }

            /**
             * @param string $currentPage
             * @param object $dataToProcess
             * @return void
             * @throws Exception
             */
            public function validate(string $currentPage, object $dataToProcess): void
            {
                //the unibe omnitracker backend has already a validation
                //implement assert by schema-definitions where it's additionally mandatory - throw end user readable exceptions - with a dedicated Exception Handler if invalid
            }

            public function mapArrayToString(object $srcObject, object $objectToMapTo, string $mapFromKey, string $mapToKey): object
            {
                if (property_exists($srcObject, $mapFromKey)) {
                    $objectToMapTo->{$mapToKey} = (is_string($srcObject->{$mapFromKey}) || is_null($srcObject->{$mapFromKey})) ? $srcObject->{$mapFromKey} : implode(", ", $srcObject->{$mapFromKey});
                    return $objectToMapTo;
                }
                return $objectToMapTo;
            }


            public function mapSendTo(?bool $sendToParents, object $objectToMapTo, string $mapToParentsKey, string $mapToStudentKey): object
            {
                if ($sendToParents === true) {
                    $objectToMapTo->{$mapToParentsKey} = true;
                    $objectToMapTo->{$mapToStudentKey} = false;
                    return $objectToMapTo;
                }
                $objectToMapTo->{$mapToParentsKey} = false;
                $objectToMapTo->{$mapToStudentKey} = true;

                return $objectToMapTo;
            }

            /**
             * @param object $srcObject
             * @param object $objectToMapTo
             * @param WorkflowOutputDefinition $dataToProcessAttributesDefinition
             * @param BaseDataItemAttributesDefinition $itemAttributesDefinitionToMapTo
             * @param object|null $stateObject
             * @return object
             *
             * the target api has only one phone number field. with this mapping the last mapped number will be used. we handle the number mapping with the following order
             * - stateObject
             * - business number
             * - home number
             * - mobile number
             */
            public function mapPhoneNumber(object $srcObject, object $objectToMapTo, WorkflowOutputDefinition $dataToProcessAttributesDefinition, BaseDataItemAttributesDefinition $itemAttributesDefinitionToMapTo): object
            {

                $extractAndFormatNumber = function ($number, $countryCode) {
                    $phoneNumberUtil = PhoneNumberUtil::getInstance(PhoneNumberUtil::META_DATA_FILE_PREFIX);
                    $regionCode = $phoneNumberUtil->getRegionCodeForCountryCode($countryCode);
                    $number = $phoneNumberUtil->parse($number, $regionCode);
                    return $phoneNumberUtil->format($number, PhoneNumberFormat::INTERNATIONAL);
                };


                $fromBusinessKey = $dataToProcessAttributesDefinition->businessPhoneNumber->name;
                $fromBusinessAreaKey = $dataToProcessAttributesDefinition->businessPhoneAreaCode->name;

                $fromHomeKey = $dataToProcessAttributesDefinition->homePhoneNumber->name;
                $fromHomeAreaKey = $dataToProcessAttributesDefinition->homePhoneAreaCode->name;

                $fromMobileKey = $dataToProcessAttributesDefinition->mobilePhoneNumber->name;
                $fromMobileAreaKey = $dataToProcessAttributesDefinition->mobilePhoneAreaCode->name;


                $toPhoneTypeValueBusiness = "business";
                $toPhoneTypeValueHome = "home";
                $toPhoneTypeValueMobile = "mobile";

                $toPhoneType = $itemAttributesDefinitionToMapTo->telefontyp->name;
                $toAreaKey = $itemAttributesDefinitionToMapTo->countrycode->name;
                $toNumberKey = $itemAttributesDefinitionToMapTo->telefon->name;


                if (property_exists($srcObject, $fromBusinessKey) && empty($srcObject->{$fromBusinessKey}) === false) {
                    $objectToMapTo->{$toPhoneType} = $toPhoneTypeValueBusiness;
                    $objectToMapTo->{$toNumberKey} = $extractAndFormatNumber($srcObject->{$fromBusinessKey}, $srcObject->{$fromBusinessAreaKey});
                    $objectToMapTo->{$toAreaKey} = $srcObject->{$fromBusinessAreaKey};
                }

                if (property_exists($srcObject, $fromHomeKey) && empty($srcObject->{$fromHomeKey}) === false) {
                    $objectToMapTo->{$toPhoneType} = $toPhoneTypeValueHome;
                    $objectToMapTo->{$toNumberKey} = $extractAndFormatNumber($srcObject->{$fromHomeKey}, $srcObject->{$fromHomeAreaKey});
                    $objectToMapTo->{$toAreaKey} = $srcObject->{$fromHomeAreaKey};
                }

                if (property_exists($srcObject, $fromMobileKey) && empty($srcObject->{$fromMobileKey}) === false) {
                    $objectToMapTo->{$toPhoneType} = $toPhoneTypeValueMobile;
                    $objectToMapTo->{$toNumberKey} = $extractAndFormatNumber($srcObject->{$fromMobileKey}, $srcObject->{$fromMobileAreaKey});
                    $objectToMapTo->{$toAreaKey} = $srcObject->{$fromMobileAreaKey};
                }

                return $objectToMapTo;
            }
        };
    }


}