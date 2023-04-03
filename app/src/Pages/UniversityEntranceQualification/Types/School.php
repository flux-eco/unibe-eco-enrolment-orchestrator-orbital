<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types;

interface School
{
    public function getId(): string;

    public function getLabel(): Label;

    public function getSchoolTypeId(): string;

    public function getCertificateId(): string;

    public function getCantonId(): string;
}