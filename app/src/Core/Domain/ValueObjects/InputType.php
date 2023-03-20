<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

enum InputType: int
{
    case CERTIFICATE_TYPE = 0;
    case CERTIFICATE = 2;
    case ISSUE_YEAR = 1;
}