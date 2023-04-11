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
        return json_encode(
            $this->appendDebugData(
                $this->resolveDynamicData(
                    $this->resolveStateData(json_decode($pageJson), $transactionStateObject),
                    $transactionStateObject
                ),
                $transactionStateObject
            )
        );
    }

    /**
     * @throws Exception
     */
    public function storeData(string $currentPage, FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
    {
        if ($transactionStateObject->data === null) {
            return $this->createEnrolment($currentPage, $transactionStateObject, $dataToProcess);
        }
        return $this->updateEnrolment($currentPage, $transactionStateObject, $dataToProcess);
    }

    private function createEnrolment(string $currentPage, FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
    {
        $createdStateData = $this->config->outbounds->processCreateEnrolment($currentPage, $transactionStateObject, $dataToProcess);

        print_r($createdStateData);

        $createdTransactionStateObject = FluxEcoTransactionStateObject::createNew(
            $transactionStateObject->transactionId,
            $createdStateData,
            $currentPage
        );
        //update Enrolment - e.g. to store the semester
        return $this->updateEnrolment($currentPage, $createdTransactionStateObject, $dataToProcess);
    }

    private function updateEnrolment($currentPage, FluxEcoTransactionStateObject $transactionStateObject, object $dataToProcess): object
    {
        return $this->config->outbounds->processUpdateEnrolment($currentPage, $transactionStateObject, $dataToProcess, $this->config->settings->enrolmentDefinition->outputDataObjectDefinition);
    }

    public function readLayout(): string
    {
        $layoutItemDefinition = $this->config->settings->enrolmentDefinition->layout;
        return $this->config->outbounds->processReadJsonFile(
            $layoutItemDefinition->stateFilePath->directoryPath, $layoutItemDefinition->stateFilePath->fileName
        );
    }

    private function readSubjectData(FluxEcoTransactionStateObject $transactionStateObject): array
    {
        $definition = $this->config->settings->enrolmentDefinition->inputOptions->subjects;
        $data = json_decode($this->config->outbounds->processReadJsonFile(
            $definition->stateFilePath->directoryPath, $definition->stateFilePath->fileName
        ), true);
        return $data[$transactionStateObject->data->StudienstufeUniqueId];
    }

    private function readSubjectCombinationData(FluxEcoTransactionStateObject $transactionStateObject): array
    {
        $definition = $this->config->settings->enrolmentDefinition->inputOptions->subjectCombinations;
        $data = json_decode($this->config->outbounds->processReadJsonFile(
            $definition->stateFilePath->directoryPath, $definition->stateFilePath->fileName
        ), true);
        return $data[$transactionStateObject->data->StudienstrukturUniqueId];
    }

    private function readChoosenSubjectData(FluxEcoTransactionStateObject $transactionStateObject): object
    {
        $label = new \stdClass();
        $label->de = $transactionStateObject->data->Studiengangsversion;

        $object = new \stdClass();
        $object->id = $transactionStateObject->data->StudiengangsversionUniqueId;
        $object->label = $label;
        $object->ect = $transactionStateObject->data->StudiengangsversionReqEcts;

        return $object;
    }

    private function appendDebugData(object|array $data, FluxEcoTransactionStateObject $transactionStateObject): object|array
    {
        if ($this->config->settings->debug === true) {
            $data->debugData = $transactionStateObject;
            return $data;
        }
    }

    /**
     * @throws Exception
     * todo find a more generic way
     */
    private function resolveDynamicData(object|array $data, FluxEcoTransactionStateObject $transactionStateObject): object|array
    {
        if (is_object($data) === true) {
            $dataItems = get_object_vars($data);
        } else {
            $dataItems = $data;
        }

        $resolvedData = new \stdClass();
        foreach ($dataItems as $key => $value) {
            if (is_object($value) === true) {
                if (property_exists($value, '$dynamic')) {
                    $method = $value->{'$dynamic'};
                    $resolvedData->{"$key"} = $this->{$method}($transactionStateObject);
                    continue;
                }
                $resolvedData->{"$key"} = $this->resolveDynamicData($value, $transactionStateObject);
                continue;
            }
            $resolvedData->{"$key"} = $value;
        }

        if (is_object($data) === true) {
            return $resolvedData;
        }
        return (array)$resolvedData;
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