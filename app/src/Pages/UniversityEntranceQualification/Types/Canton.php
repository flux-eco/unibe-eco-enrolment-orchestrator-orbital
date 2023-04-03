<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types;

interface Canton
{
    public function getId(): string;
    public function getLabel(): Label;
}