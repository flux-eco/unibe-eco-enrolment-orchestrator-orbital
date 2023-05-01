<?php

namespace FluxEco\UnibeEnrolment;

use Exception;
use FluxEcoType\FluxEcoStateValues;
use stdClass;

final readonly class Api
{
    private function __construct(
        private State $state
    )
    {

    }

    public static function new(State $state): self
    {
        return new self($state);
    }

    public function readStateNames(): StateNames|stdClass {
        return $this->state->stateNames;
    }

    /**
     * @throws Exception
     */
    public function readTransactionStateValues(?object $stateValues, callable $objectFromJsonFile): FluxEcoStateValues|stdClass
    {
        return $this->state->readTransactionStateValues($stateValues, $objectFromJsonFile);
    }

    public function processData(StateValues|stdClass $stateValues, object $processData, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile): FluxEcoStateValues|stdClass
    {
        return $this->state->processData($stateValues, $processData, $storeNewEnrolment, $updateEnrolment, $objectFromJsonFile);
    }

}