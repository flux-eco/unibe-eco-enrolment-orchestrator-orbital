<?php

namespace FluxEco\HttpWorkflowRequestHandler\Types;

use FluxEcoType\FluxEcoTransactionStateObject;

interface OutboundsActionsProcessor
{
    public function processCreateTransactionId(): string;

    public function processReadStartPageName(): string;

    public function isLastPage(FluxEcoTransactionStateObject $transactionStateObject): bool;

    public function isStartPage(string $pageName): bool;

    public function isResumePage(string $pageName): bool;

    public function processReadResumeEnrolmentData(string $transactionId, object $processData): object;

    public function processReadLastHandledPageNameFromWorkflowState(object $workflowState): string;

    public function processReadPreviousPageName(FluxEcoTransactionStateObject $transactionStateObject): string;

    public function processReadCurrentPage(FluxEcoTransactionStateObject $transactionStateObject): string;

    public function processReadNextPageName(string $lastHandledPageName, FluxEcoTransactionStateObject $transactionStateObject): string;

    public function processStoreRequestContent(FluxEcoTransactionStateObject $transactionStateObject, object $processData): object;

    public function processReadLayout(): string;
}