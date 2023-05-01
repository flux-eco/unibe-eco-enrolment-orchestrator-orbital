<?php

namespace FluxEco\HttpWorkflowRequestHandler;

use Exception;
use FluxEcoType\FluxEcoStateMonad;
use FluxEcoType\FluxEcoStateValues;
use stdClass;

final class State
{
    /**
     * @property StateData $initData
     */
    private object $initData;
    public StateNames|stdClass $stateNames;

    private function __construct(
        string $layoutDataDirectory,
        string $layoutFileName,
    )
    {
        /**
         * @var StateNames|stdClass $stateNames
         */
        $stateNames = new stdClass();
        $stateNames->transactionIdCookieName = "transactionIdCookieName";
        $stateNames->init = "init";
        $stateNames->failed = "failed";
        $stateNames->setTransactionStateValues = "setTransactionStateValues";
        $stateNames->updateTransactionStateValues = "updateTransactionStateValues";
        $stateNames->setContent = "setContent";
        $stateNames->processData = "processData";
        $stateNames->storeTransactionStateValues = "storeTransactionStateValues";
        $this->stateNames = $stateNames;

        /**
         * @var StateData|stdClass $initData
         */
        $initData = new stdClass();
        $initData->name = "flux-eco-http-workflow-request-handler";
        $initData->layoutDataDirectory = $layoutDataDirectory;
        $initData->layoutFileName = $layoutFileName;
        $initData->transactionIdCookieName = sprintf('%s/%s', $initData->name, "transaction-id");

        $this->initData = $initData;
    }

    /**
     * @return self
     */
    public static function newEmpty(
        string $layoutDataDirectory,
        string $layoutFileName,
    ): State
    {
        return new self(...get_defined_vars());
    }

    /**
     * @throws Exception
     */
    public function contentByGetRequest(
        string   $requestUri,
        callable $objectFromJsonFile,
        callable $readCookie,
        callable $storeCookie,
        callable $readTransactionStateValuesFromCache,
        callable $storeTransactionStateValuesInCache,
        callable $readTransactionStateValuesFromManager
    ): object
    {
        try {
            return match ($requestUri) {
                "/api/layout" => $this->setLayoutFlow($objectFromJsonFile)->data->content,
                "/api/get" => $this->setTransactionContentFlow($readCookie, $storeCookie, $readTransactionStateValuesFromCache, $storeTransactionStateValuesInCache, $readTransactionStateValuesFromManager)->data->content
            };
        } catch (\UnhandledMatchError $e) {
            var_dump($e);
        }
    }

    /**
     * @throws Exception
     */
    public function processDataFromPostRequest(
        object   $dataToProcess,
        callable $readCookie,
        callable $storeCookie,
        callable $readTransactionStateValuesFromCache,
        callable $storeTransactionStateValuesInCache,
        callable $readTransactionStateValuesFromManager,
        callable $processDataByTransactionStateManager
    ): object
    {
        return $this->processDataFlow($dataToProcess, $readCookie, $storeCookie, $readTransactionStateValuesFromCache, $storeTransactionStateValuesInCache, $readTransactionStateValuesFromManager, $processDataByTransactionStateManager)->data->content;
    }

    /**
     * @throws Exception
     */
    public function processDataFlow(
        object   $dataToProcess,
        callable $readCookie,
        callable $storeCookie,
        callable $readTransactionStateValuesFromCache,
        callable $storeTransactionStateValuesInCache,
        callable $readTransactionStateValuesFromManager,
        callable $processDataByTransactionStateManager
    ): StateValues|FluxEcoStateValues|stdClass
    {
        $transitionFlow = [$this->stateNames->setTransactionStateValues, $this->stateNames->updateTransactionStateValues, $this->stateNames->processData, $this->stateNames->storeTransactionStateValues];
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = $this->initStateValues($transitionFlow);


        return FluxEcoStateMonad::of($stateValues)
            ->bind(fn(StateValues|stdClass $stateValues) => $this->setTransactionStateValuesFromCache($stateValues, $stateValues->data->transactionIdCookieName, $readCookie, $readTransactionStateValuesFromCache))
            ->bind(fn(StateValues|stdClass $stateValues) => $this->updateTransactionStateValuesFromTransactionStateManager($stateValues, $stateValues->data->transactionStateValues, $readTransactionStateValuesFromManager))
            ->bind(fn(StateValues|stdClass $stateValues) => $this->processData($stateValues, $dataToProcess, $storeTransactionStateValuesInCache, $processDataByTransactionStateManager))
            ->bind(fn(StateValues|stdClass $stateValues) => $this->storeTransactionStateValues($stateValues, $stateValues->data->transactionIdCookieName, $stateValues->data->transactionStateValues->data->transactionId, $stateValues->data->transactionStateValues, $storeCookie, $storeTransactionStateValuesInCache))->stateValues;
    }

    /**
     * @throws Exception
     */
    public function setTransactionContentFlow(
        callable $readCookie,
        callable $storeCookie,
        callable $readTransactionStateValuesFromCache,
        callable $storeTransactionStateValuesInCache,
        callable $readTransactionStateValuesFromManager
    ): StateValues|FluxEcoStateValues|stdClass
    {
        $transitionFlow = [$this->stateNames->setTransactionStateValues, $this->stateNames->updateTransactionStateValues, $this->stateNames->setContent, $this->stateNames->storeTransactionStateValues];
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = $this->initStateValues($transitionFlow);

        return FluxEcoStateMonad::of($stateValues)
            ->bind(fn(StateValues|stdClass $stateValues) => $this->setTransactionStateValuesFromCache($stateValues, $stateValues->data->transactionIdCookieName, $readCookie, $readTransactionStateValuesFromCache))
            ->bind(fn(StateValues|stdClass $stateValues) => $this->updateTransactionStateValuesFromTransactionStateManager($stateValues, $stateValues->data->transactionStateValues, $readTransactionStateValuesFromManager))
            ->bind(fn(StateValues|stdClass $stateValues) => $this->setContentFromTransactionStateValues($stateValues, $stateValues->data->transactionStateValues))
            ->bind(fn(StateValues|stdClass $stateValues) => $this->storeTransactionStateValues($stateValues, $stateValues->data->transactionIdCookieName, $stateValues->data->transactionStateValues->data->transactionId, $stateValues->data->transactionStateValues, $storeCookie, $storeTransactionStateValuesInCache))->stateValues;
    }

    /**
     * @throws Exception
     */
    public function storeTransactionStateValues(StateValues|stdClass $stateValues, string $transactionIdCookieName, string $transactionId, FluxEcoStateValues|stdClass $transactionStateValues, callable $storeCookie, callable $storeTransactionStateValuesInCache) {
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->storeTransactionStateValues);
        $storeCookie($transactionIdCookieName, $transactionId);
        $storeTransactionStateValuesInCache($transactionId, $transactionStateValues);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->storeTransactionStateValues), null];
    }

    /**
     * @throws Exception
     */
    public function processData(StateValues|stdClass $stateValues, object $dataToProcess, callable $storeTransactionStateValuesInCache, callable $processDataByTransactionStateManager): array
    {
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processData);

        $transactionStateValues = $processDataByTransactionStateManager($stateValues->data->transactionStateValues, $dataToProcess);
        $storeTransactionStateValuesInCache($transactionStateValues->data->transactionId, $transactionStateValues);

        $stateData = $stateValues->data;
        $stateData->transactionStateValues = $transactionStateValues;
        $stateData->content = new stdClass();
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processData), null];
    }

    /**
     * @throws Exception
     */
    public function setLayoutFlow(callable $objectFromJsonFile): StateValues|FluxEcoStateValues|stdClass
    {
        $transitionFlow = [$this->stateNames->setContent];
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = $this->initStateValues($transitionFlow);

        return FluxEcoStateMonad::of($stateValues)
            ->bind(fn(StateValues|stdClass $stateValues) => $this->setContentByLayout($stateValues, $objectFromJsonFile, $stateValues->data->layoutDataDirectory, $stateValues->data->layoutFileName))->stateValues;
    }

    /**
     * @throws Exception
     */
    public function setContentByLayout(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $layoutDataDirectory, string $layoutFileName): array
    {
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->setContent);

        $stateData = $stateValues->data;
        $stateData->content = $objectFromJsonFile($layoutDataDirectory, $layoutFileName);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->setContent), null];
    }


    /**
     * @throws Exception
     */
    public function setTransactionStateValuesFromCache(FluxEcoStateValues|stdClass $stateValues, string $transactionIdCookieName, callable $readCookie, callable $readTransactionStateValuesFromCache): array
    {
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->setTransactionStateValues);

        $stateData = $stateValues->data;
        $transactionId = $readCookie($transactionIdCookieName);
        $transactionStateValues = $readTransactionStateValuesFromCache($transactionId);

        if ($readCookie($transactionIdCookieName) === null || $transactionStateValues === null) {
            $stateData->transactionStateValues = null;
            $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);
            return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->setTransactionStateValues), null];
        }

        $stateData->transactionStateValues = $transactionStateValues;
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);
        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->setTransactionStateValues), null];
    }

    /**
     * @throws Exception
     */
    public function updateTransactionStateValuesFromTransactionStateManager(StateValues|stdClass $stateValues, null|FluxEcoStateValues|stdClass $transactionStateValues, callable $readTransactionStateValuesFromManager): array
    {
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->updateTransactionStateValues);

        $stateData = $stateValues->data;
        $stateData->transactionStateValues = $readTransactionStateValuesFromManager($transactionStateValues);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->updateTransactionStateValues), null];
    }


    /**
     * @throws Exception
     */
    public function setContentFromTransactionStateValues(StateValues|stdClass        $stateValues,
                                                         FluxEcoStateValues|stdClass $transactionStatevalues): array
    {
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->setContent);

        $stateData = $stateValues->data;

        $stateData->content = $transactionStatevalues->data->content;

        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->setContent), null];
    }


    private function initStateValues(array $transitionFlow): StateValues|FluxEcoStateValues|stdClass
    {
        if (count($transitionFlow) >= 2) {
            $nextStateName = $transitionFlow[1];
        } else {
            $nextStateName = $transitionFlow[(count($transitionFlow) - 1)];
        }

        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = new stdClass();
        $stateValues->currentStateName = $transitionFlow[0];
        $stateValues->nextStateName = $nextStateName;
        $stateValues->finalStateName = $transitionFlow[(count($transitionFlow) - 1)];
        $stateValues->completedTransitionNames = [];
        $stateValues->uncompletedTransitionNames = $transitionFlow;
        $stateValues->data = $this->initData;

        return $stateValues;
    }
}