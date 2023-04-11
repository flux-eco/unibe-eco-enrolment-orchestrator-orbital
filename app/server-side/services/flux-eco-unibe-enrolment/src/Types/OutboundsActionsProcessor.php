<?php

namespace FluxEco\UnibeEnrolment\Types;

use FluxEco\UnibeEnrolment\Types\Enrolment\WorkflowOutputDefinition;
use FluxEcoType\FluxEcoTransactionStateObject;

interface OutboundsActionsProcessor
{
    public function processReadJsonFile(string $directoryPath, string $jsonFileName): string;

    public function processCreateEnrolment(string $currentPage, FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object;

    public function processUpdateEnrolment(string $currentPage, FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess, WorkflowOutputDefinition $dataToProcessAttributesDefinition): object;
}