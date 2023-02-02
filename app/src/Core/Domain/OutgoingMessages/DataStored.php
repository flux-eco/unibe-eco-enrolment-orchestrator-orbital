<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\EnrolmentData;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\PageName;

final readonly class DataStored
{

    private function __construct(
        public PageName      $pageName,
        public string        $sessionId,
        public EnrolmentData $enrolmentData,
        public bool          $ok,
        public array         $errorMessages
    )
    {
    }

    public static function new(
        PageName      $pageName,
        string $sessionId,
        EnrolmentData $enrolmentData,
        bool          $ok,
        array         $errorMessages = []
    )
    {
        return new self($pageName, $sessionId, $enrolmentData, $ok, $errorMessages);
    }
}