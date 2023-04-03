<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types;

interface CertificateType
{
    public function getId(): string;
    public function getLabel(): Label;
    public function getMunicipalityRequired(): bool;
}