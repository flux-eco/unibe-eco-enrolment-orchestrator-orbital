<?php

namespace FluxEco\HttpWorkflowRequestHandler\Types;

use FluxEcoType\FluxEcoTransactionStateObject;

interface OutboundsActionsProcessor
{
    public function processCreateTransactionId(): string;

    public function processReadCurrentPage(FluxEcoTransactionStateObject $transactionStateObject): string;

    public function processStoreRequestContent(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object;

    public function processReadLayout(): string;
}