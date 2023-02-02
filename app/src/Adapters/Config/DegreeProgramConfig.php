<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config;
final readonly class DegreeProgramConfig {
    private function __construct(
        public int $uniqueId,
        public array $bfsCodes
    ) {

    }

    public static function new(
        int $uniqueId,
        array $bfsCodes
    ): self  {
        return new self(...get_defined_vars());
    }
}