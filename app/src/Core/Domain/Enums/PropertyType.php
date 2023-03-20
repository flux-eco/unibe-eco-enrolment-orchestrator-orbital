<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums;
enum PropertyType: string
{
    case ENTITY_KEY = "entityKey";
    case ISSUE_PERIOD = "issuePeriod";
    case LABEL = "label";

    case CERTIFICATE_IDS = "certificateIds";

    case CERTIFICATE_TYPE_ID = "certificateTypeId";

    public function apply(\stdClass $obj, $propertyValue): object
    {
        $obj->{$this->value} = $propertyValue;
        return $obj;
    }

    public function read(\stdClass $obj): mixed
    {
        return $obj->{$this->value};
    }

    public function exists(\stdClass $obj): bool
    {
        return property_exists($obj, $this->value);
    }
}