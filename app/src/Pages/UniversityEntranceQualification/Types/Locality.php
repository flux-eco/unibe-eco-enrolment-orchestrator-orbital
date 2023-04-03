<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types;
interface Locality
{
    public function getId(): string;

    public function getLabel(): Label;

    public function getPlz(): int;

    public function getCantonId(): string;
}