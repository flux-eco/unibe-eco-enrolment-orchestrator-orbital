<?php

namespace FluxEco\UnibeEnrolment\Types;

use FluxEco\UnibeEnrolment\Types\Enrolment\WorkflowOutputDefinition;
use FluxEcoType\FluxEcoTransactionStateObject;

interface OutboundsActionsProcessor
{
    public function processReadJsonFile(string $directoryPath, string $jsonFileName): string;

    public function processReadResumeEnrolmentData(string $transactionId, string $identificationNumber, string $password): object;

    public function processCreateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, string $password): object;

    public function processUpdateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess, WorkflowOutputDefinition $dataToProcessAttributesDefinition): object;
}