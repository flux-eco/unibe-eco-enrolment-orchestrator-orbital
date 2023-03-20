<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class CertificateType
{
    /**
     * @param string $id
     * @param ValueObjects\Label $label
     * @param bool $municipalityRequired
     */
    private function __construct(
        public string $id,
        public ValueObjects\Label $label,
        public bool $municipalityRequired,
    )
    {

    }

    /**
     * @param string $id
     * @param ValueObjects\Label $label
     * @param bool $municipalityRequired
     * @return static
     */
    public static function new(
        string $id,
        ValueObjects\Label $label,
        bool $municipalityRequired,
    ): self
    {
        return new self($id, $label, $municipalityRequired);
    }
}