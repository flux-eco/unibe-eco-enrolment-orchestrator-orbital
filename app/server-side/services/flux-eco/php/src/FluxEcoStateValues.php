<?php

namespace FluxEcoType;

use stdClass;

/**
 * @property string $currentStateName
 * @property string $nextStateName
 * @property string $finalStateName
 * @property array $completedTransitionNames
 * @property array $uncompletedTransitionNames
 * @property ?stdClass $data
 */
interface FluxEcoStateValues {
/*
    private function __construct(
        public string $currentStateName,
        public string $nextStateName,
        public string $finalStateName,
        public array $completedTransitionNames,
        public array $uncompletedTransitionNames,
        public ?object $data
    ) {

    }

    public static function new (
        string $currentStateName,
        string $nextStateName,
        string $finalStateName,
        array $completedTransitionNames,
        array $uncompletedTransitionNames,
        ?object $data
    ): self {
        return new self(...get_defined_vars());
    }
*/
}