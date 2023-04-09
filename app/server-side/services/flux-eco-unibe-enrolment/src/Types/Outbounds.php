<?php

namespace FluxEco\UnibeEnrolment\Types;

use FluxEco\UnibeEnrolment\Types\Enrolment\OutputDataObject;

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

    public function processCreateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
    {
        return $this->actionProcessor->processCreateEnrolment($transactionStateObject, $dataToProcess);
    }

    public function processUpdateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess, OutputDataObject $dataToProcessAttributesDefinition): object
    {
        return $this->actionProcessor->processUpdateEnrolment($transactionStateObject, $dataToProcess, $dataToProcessAttributesDefinition);
    }
}