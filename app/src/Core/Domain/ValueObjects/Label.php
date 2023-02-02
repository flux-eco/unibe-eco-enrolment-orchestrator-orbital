<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class Label {
    private function __construct(
        public string $en,
        public string $de,
    ) {

    }

    public static function new(
        string $en,
        string $de
    ) {
        return new self($en, $de);
    }
}