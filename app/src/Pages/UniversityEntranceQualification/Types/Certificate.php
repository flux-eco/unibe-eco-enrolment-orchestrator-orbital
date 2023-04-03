<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types;

interface Certificate
{
    public function getId(): string;
    public function getLabel(): Label;
    public function getMinIssueYear(): int;
    public function getMaxIssueYear(): int;
    public function getCertificateTypeId(): int;
}