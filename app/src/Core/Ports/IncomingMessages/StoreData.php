<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\IncomingMessages;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class StoreData
{

    private function __construct(
        public ValueObjects\PageName $pageName,
        public string $sessionId,
        public object $dataToStore,
        public ValueObjects\EnrolmentData $enrolmentData
    )
    {

    }

    public static function new(
        ValueObjects\PageName $pageName,
        string $sessionId,
        object                $dataToStore,
        ValueObjects\EnrolmentData $enrolmentData
    ): self
    {

        return new self(
            ...get_defined_vars()
        );
    }
}