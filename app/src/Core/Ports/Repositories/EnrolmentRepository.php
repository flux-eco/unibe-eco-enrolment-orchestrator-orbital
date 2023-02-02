<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\Repositories;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ReferenceObjects\BaseData;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\LanguageCode;

interface EnrolmentRepository
{
    public function create(string $sessionId, string $password, LanguageCode $languageCode): BaseData;
    public function storeBaseData(string $sessionId, object $baseData, LanguageCode $languageCode): object;
}