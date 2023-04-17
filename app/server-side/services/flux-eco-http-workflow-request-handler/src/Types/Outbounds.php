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

    public function processReadStartPageName(): string
    {
        return $this->actionProcessor->processReadStartPageName();
    }

    public function isStartPage(string $pageName): bool
    {
        return $this->actionProcessor->isStartPage($pageName);
    }

    public function isResumePage(string $pageName): bool
    {
        return $this->actionProcessor->isResumePage($pageName);
    }

    public function isLastPage(FluxEcoTransactionStateObject $transactionStateObject): bool //todo change parameter to string: pageName
    {
        return $this->actionProcessor->isLastPage($transactionStateObject);
    }

    public function processReadPreviousPageName(FluxEcoTransactionStateObject $transactionStateObject): string
    {
        return $this->actionProcessor->processReadPreviousPageName($transactionStateObject);
    }

    public function processReadResumeEnrolmentData(string $transactionId, object $processData): object {
        return $this->actionProcessor->processReadResumeEnrolmentData($transactionId, $processData);
    }

    public function processReadCurrentPage(FluxEcoTransactionStateObject $transactionStateObject): string
    {
        return $this->actionProcessor->processReadCurrentPage($transactionStateObject);
    }

    public function processStoreRequestContent(FluxEcoTransactionStateObject $transactionStateObject, object $processData): object
    {
        return $this->actionProcessor->processStoreRequestContent($transactionStateObject, $processData);
    }

    public function processReadLayout(): string
    {
        return $this->actionProcessor->processReadLayout();
    }

    public function processCreateTransactionId(): string
    {
        return $this->actionProcessor->processCreateTransactionId();
    }

    public function processReadNextPageName(string $lastHandledPageName, FluxEcoTransactionStateObject $transactionStateObject): string
    {
        return $this->actionProcessor->processReadNextPageName($lastHandledPageName, $transactionStateObject);
    }

    public function processReadLastHandledPageNameFromWorkflowState(object $workflowState): string
    {
        return $this->actionProcessor->processReadLastHandledPageNameFromWorkflowState($workflowState);
    }
}