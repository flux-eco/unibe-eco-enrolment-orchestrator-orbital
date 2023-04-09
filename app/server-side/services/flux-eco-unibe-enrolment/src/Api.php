<?php

namespace FluxEco\UnibeEnrolment;

use Exception;
use FluxEcoType\FluxEcoTransactionStateObject;

final readonly class Api
{
    private function __construct(private Config $config)
    {

    }

    public static function new(Types\Outbounds $outbounds): self
    {
        return new self(Config::new($outbounds));
    }

    public function readEnrolmentDefinition(): Types\Enrolment\EnrolmentDefinition
    {
        return $this->config->settings->enrolmentDefinition;
    }

    /**
     * @throws Exception
     */
    public function readPage(FluxEcoTransactionStateObject $transactionStateObject): string
    {
        $pages = $this->config->settings->enrolmentDefinition->pages;
        if ($transactionStateObject->transactionId === null) {
            return $this->config->outbounds->processReadJsonFile(
                $pages->start->stateFilePath->directoryPath, $pages->start->stateFilePath->fileName
            );
        }

        $nextPageAttributeDefinition = $pages->{$this->config->settings->enrolmentDefinition->workflow->getNextPage($transactionStateObject->lastHandledPage, $transactionStateObject)};
        $pageJson = $this->config->outbounds->processReadJsonFile($nextPageAttributeDefinition->stateFilePath->directoryPath, $nextPageAttributeDefinition->stateFilePath->fileName);
        return json_encode($this->resolveStateData(json_decode($pageJson), $transactionStateObject));
    }

    /**
     * @throws Exception
     */
    public function storeData(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
    {
        if ($transactionStateObject->data === null) {
            return $this->createEnrolment($transactionStateObject, $dataToProcess);
        }
        return $this->updateEnrolment($transactionStateObject, $dataToProcess);
    }

    private function createEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
    {
        return $this->config->outbounds->processCreateEnrolment($transactionStateObject, $dataToProcess);
    }

    private function updateEnrolment(FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
    {
        return $this->config->outbounds->processUpdateEnrolment($transactionStateObject, $dataToProcess, $this->config->settings->enrolmentDefinition->outputDataObject);
    }

    public function readLayout(): string
    {
        $layoutItemDefinition = $this->config->settings->attributesDefinition->getLayoutAttributeDefinition();
        return $this->config->outbounds->processReadJsonFile(
            $layoutItemDefinition->stateFilePath->directoryPath, $layoutItemDefinition->stateFilePath
        );
    }

    /**
     * @throws Exception
     */
    private function resolveStateData(object|array $data, FluxEcoTransactionStateObject $transactionStateObject): object|array
    {
        if (is_object($data) === true) {
            $dataItems = get_object_vars($data);
        } else {
            $dataItems = $data;
        }

        $resolvedStateData = new \stdClass();
        foreach ($dataItems as $key => $value) {
            if (is_object($value) === true) {
                if (property_exists($value, '$state')) {
                    $dottedKeyPath = $value->{'$state'};
                    $keyPathParts = explode(".", $dottedKeyPath);
                    $linkedValue = $transactionStateObject;
                    foreach ($keyPathParts as $attributeKey) {
                        $linkedValue = $linkedValue->{$attributeKey};
                    }
                    $resolvedStateData->{"$key"} = $linkedValue;
                    continue;
                }
                $resolvedStateData->{"$key"} = $this->resolveStateData($value, $transactionStateObject);
                continue;
            }
            $resolvedStateData->{"$key"} = $value;
        }

        if (is_object($data) === true) {
            return $resolvedStateData;
        }
        return (array)$resolvedStateData;
    }

}