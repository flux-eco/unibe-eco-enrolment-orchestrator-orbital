<?php

namespace FluxEcoType;

use Exception;
use stdClass;

final readonly class FluxEcoStateMonad
{
    public static function of(FluxEcoStateValues|stdClass $stateValues): FluxEcoStateMonad
    {
        return new self($stateValues);
    }

    private function __construct(public FluxEcoStateValues|stdClass $stateValues)
    {

    }

    public function bind(callable $function): FluxEcoStateMonad
    {
        [$newState, $newFunction] = $function($this->stateValues);

        if ($newFunction === null) {
            //final transition of chain
            return FluxEcoStateMonad::of($newState);
        }

        return self::of($newState)->bind($newFunction);
    }


    /**
     * @throws Exception
     */
    public static function markStateAsCompleted(FluxEcoStateValues|stdClass $stateValues, string $currentStateName): FluxEcoStateValues|stdClass
    {
        if ($stateValues->currentStateName !== $currentStateName) {
            throw new Exception("the current transition stateName differs from the initialized transition steps");
        }

        $index = array_search($currentStateName, $stateValues->uncompletedTransitionNames);

        if ($index !== -1) {
            $uncompletedTranistionsNames = $stateValues->uncompletedTransitionNames;
            array_splice($uncompletedTranistionsNames, $index, 1);

            $completedTranistionsNames = $stateValues->completedTransitionNames;
            $completedTranistionsNames[] = $currentStateName;

            if(count($uncompletedTranistionsNames) >= 2) {
                $nextStateName = $uncompletedTranistionsNames[1];
            } else {
                $nextStateName = $stateValues->finalStateName;
            }

            /**
             * @var FluxEcoStateValues|stdClass $stateValues
             */
            $newtStateValues = clone($stateValues);
            $newtStateValues->currentStateName = $stateValues->nextStateName;
            $newtStateValues->nextStateName = $nextStateName;
            $newtStateValues->uncompletedTransitionNames = $uncompletedTranistionsNames;
            $newtStateValues->completedTransitionNames = $completedTranistionsNames;

            return $newtStateValues;
        }

        return $stateValues;
    }

    public static function putStateNameAsNextInFront(FluxEcoStateValues|stdClass $stateValues, string $stateName): FluxEcoStateValues|stdClass
    {
        $uncompletedTransitions = $stateValues->uncompletedTransitionNames;

        if (!in_array($stateName, $uncompletedTransitions)) {
            array_unshift($uncompletedTransitions, $stateName);
        }

        /**
         * @var FluxEcoStateValues|stdClass $stateValues
         */
        $newtStateValues = clone($stateValues);
        $newtStateValues->currentStateName = $stateName;
        $newtStateValues->uncompletedTransitionNames = $uncompletedTransitions;
    }

    /**
     * @throws Exception
     */
    public static function changeCurrentStateName(FluxEcoStateValues|stdClass $stateValues, string $currentStateName): FluxEcoStateValues|stdClass
    {
        if ($stateValues->nextStateName !== $currentStateName && $stateValues->currentStateName !== $currentStateName) {
            throw new Exception("the current transition stateName differs from the initalized transition steps valid stepName " . $stateValues->nextStateName . " submitted stepName " . $currentStateName);
        }

        /**
         * @var FluxEcoStateValues|stdClass $stateValues
         */
        $newtStateValues = clone($stateValues);
        $newtStateValues->currentStateName = $currentStateName;
        return $newtStateValues;
    }

    public static function setStateData(FluxEcoStateValues|stdClass $stateValues, object $data): FluxEcoStateValues|stdClass
    {
        /**
         * @var FluxEcoStateValues|stdClass $stateValues
         */
        $newtStateValues = clone($stateValues);
        $newtStateValues->data = $data;
        return $newtStateValues;
    }
}