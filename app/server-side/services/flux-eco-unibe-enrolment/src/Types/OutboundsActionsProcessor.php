<?php

namespace FluxEco\UnibeEnrolment\Types;

use FluxEco\UnibeEnrolment\Types\Enrolment\OutputDataObject;
use FluxEcoType\FluxEcoTransactionStateObject;

interface OutboundsActionsProcessor
{
    public function processReadJsonFile(string $directoryPath, string $jsonFileName): string;

    public function processCreateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object;

    public function processUpdateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess, OutputDataObject $dataToProcessAttributesDefinition): object;
}