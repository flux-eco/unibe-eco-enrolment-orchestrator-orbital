<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class Year {
    private function __construct(
        public string $id
    ) {

    }

    public static function new(
        string $id,
    ) {
        return new self($id);
    }
}