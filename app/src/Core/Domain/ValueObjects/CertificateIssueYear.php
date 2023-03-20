<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class CertificateIssueYear
{

    private function __construct(
        public string $id,
    )
    {
    }

    static function new(
        string $id
    ): self
    {

        return new self(
            $id
        );
    }

}