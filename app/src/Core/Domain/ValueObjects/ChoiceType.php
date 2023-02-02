<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

enum ChoiceType: string
{
    case SINGLE_CHOICE = "single-choice";
    case MULTIPLE_CHOICE = "multiple-choice";
}