<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;

enum MessageName: string
{
    case CREATE_REFERENCE_OBJECT = "create-reference-object";
    case DATA_STORED = "data-stored";
    case ENROLMENT_CREATED = "enrolment_created";
    case PUBLISH_PAGE_OBJECT = "publish-page-object";
    case PROVIDE_CONFIGURATION_OBJECT = "provide-configuration-object";

    /**
     * @param string[] $urlParameters
     * @return void
     */
    public function toAddress(array $urlParameters = []) : string //todo
    {
        if (count($urlParameters) > 0) {
            $prefix = implode("/", $urlParameters);
            return $prefix . "/" . $this->value;
        }
        return $this->value;
    }
}