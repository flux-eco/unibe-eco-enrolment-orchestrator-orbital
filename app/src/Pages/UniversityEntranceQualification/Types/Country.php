<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types;

interface Country
{
    public function getId(): string;

    public function getLabel(): Label;

    public function getCode(): string;
}