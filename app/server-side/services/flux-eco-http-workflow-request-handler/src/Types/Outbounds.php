<?php

namespace FluxEco\HttpWorkflowRequestHandler\Types;

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

    public function processReadCurrentPage(FluxEcoTransactionStateObject $transactionStateObject): string
    {
        return $this->actionProcessor->processReadCurrentPage($transactionStateObject);
    }

    public function processStoreRequestContent(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
    {
        return $this->actionProcessor->processStoreRequestContent($transactionStateObject, $dataToProcess);
    }

    public function processReadLayout(): string
    {
        return $this->actionProcessor->processReadLayout();
    }

    public function processCreateTransactionId(): string
    {
        return $this->actionProcessor->processCreateTransactionId();
    }
}