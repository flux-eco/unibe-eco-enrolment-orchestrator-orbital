<?php

namespace FluxEco\UnibeEnrolment\Types;

use FluxEco\UnibeEnrolment\Types\Enrolment\WorkflowOutputDefinition;

use FluxEcoType\FluxEcoAttributeDefinition;
use FluxEcoType\FluxEcoTransactionStateObject;

final readonly class Outbounds implements OutboundsActionsProcessor
{

    private function __construct(
        private OutboundsActionsProcessor $actionProcessor
    )
    {

    }

    public static function new(
        OutboundsActionsProcessor $actionProcessor
    )
    {
        return new self(...get_defined_vars());
    }

    public function processReadJsonFile(string $directoryPath, string $jsonFileName): string
    {
        return $this->actionProcessor->processReadJsonFile($directoryPath, $jsonFileName);
    }

    public function processReadResumeEnrolmentData(string $transactionId, string $identificationNumber, string $password): object
    {
        return $this->actionProcessor->processReadResumeEnrolmentData($transactionId, $identificationNumber, $password);
    }

    public function processCreateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, string $password): object
    {
        return $this->actionProcessor->processCreateEnrolment($transactionStateObject, $password);
    }

    public function processUpdateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess, WorkflowOutputDefinition $dataToProcessAttributesDefinition): object
    {
        return $this->actionProcessor->processUpdateEnrolment($transactionStateObject, $dataToProcess, $dataToProcessAttributesDefinition);
    }
}