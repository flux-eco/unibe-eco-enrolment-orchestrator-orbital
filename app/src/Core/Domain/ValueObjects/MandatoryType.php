<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

enum MandatoryType: string
{
    case MANDATORY = "mandatory";
    case COMPULSORY = "compulsory";
    case CHOICE = "choice";

    public function toInteger() {
        return match($this) {
            self::MANDATORY => 1,
            self::COMPULSORY => 2,
            self::CHOICE => 3,
        };
    }
}