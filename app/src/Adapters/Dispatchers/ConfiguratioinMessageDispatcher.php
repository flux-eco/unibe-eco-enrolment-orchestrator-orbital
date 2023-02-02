<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Dispatchers;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\Dispatchers;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Repositories\EnrolmentConfigurationReferenceObjectRepository;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages\MessageName;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages\Message;

class ConfiguratioinMessageDispatcher implements Dispatchers\ConfigurationMessageDispatcher
{
    private function __construct(
        private EnrolmentConfigurationReferenceObjectRepository $enrolmentConfigurationRepository
    )
    {

    }

    public static function new(EnrolmentConfigurationReferenceObjectRepository $enrolmentConfigurationRepository): self
    {
        return new self(...get_defined_vars());
    }

    public function dispatch(Message $message): void
    {
        match (MessageName::from($message->headers->name)) {
            MessageName::CREATE_REFERENCE_OBJECT => $this->enrolmentConfigurationRepository->createReferenceObject($message->payload),
        };
    }
}