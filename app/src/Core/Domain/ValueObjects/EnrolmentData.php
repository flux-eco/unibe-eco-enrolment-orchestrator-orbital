<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use stdClass;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;

final readonly class EnrolmentData
{
    private function __construct(
        public null|Entities\BaseData|stdClass $baseData = null
    )
    {

    }

    public static function new(null|Entities\BaseData|stdClass $baseData = null): self
    {
        return new self(...get_defined_vars());
    }

    public static function fromJson(string $jsonData): self
    {
        $object = json_decode($jsonData);
        return new self($object->baseData);
    }

    public function toFrontendObject(): object {
        $object = new stdClass();
        $object->{ValueObjectName::IDENTIFICATION_NUMBER->toParameterName()} = $this->baseData->Identifikationsnummer;
        return $object;
    }
}