<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\IncomingMessages;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
final readonly class ProvidePage
{

    private function __construct(
        public string                $pageObjectDirectoryPath,
        public ValueObjects\PageName $currentPage,
        public string                $identicationNumber,
        public ?ValueObjects\EnrolmentData $enrolmentData
    ) {

    }

    public static function new(
        string                $pageObjectDirectoryPath,
        ValueObjects\PageName $currentPage,
        string                $identicationNumber = "",
        ?ValueObjects\EnrolmentData $enrolmentData = null
    ) : self {

        return new self(
            ...get_defined_vars()
        );
    }
}