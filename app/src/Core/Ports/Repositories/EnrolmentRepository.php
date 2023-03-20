<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\Repositories;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities\BaseData;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\LanguageCode;

interface EnrolmentRepository
{
    public function create(string $sessionId, string $password, LanguageCode $languageCode): BaseData;
    public function storeBaseData(string $sessionId, object $baseData, LanguageCode $languageCode): object;
}