<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Api;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Repositories;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Dispatchers;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\Config;

final readonly class CliApi
{
    private function __construct(
        private Config        $config,
        private Ports\Service $service
    )
    {

    }

    public static function new(): self
    {
        $config = Config::new();
        return new self(
            $config,
            Ports\Service::new(
                Ports\Outbounds::new(
                    Dispatchers\ConfiguratioinMessageDispatcher::new(
                        Repositories\EnrolmentConfigurationReferenceObjectRepository::new(
                            $config->configFilesDirectoryPath,
                            $config->soapWsdlServer,
                            $config->soapServerHost,
                            $config->credentials,
                            $config->degreeProgramSubjectFilter
                        )
                    ),
                    Dispatchers\EnrolmentMessageDispatcher::new(),
                    Repositories\EnrolmentRepository::new(
                        $config->soapWsdlServer,
                        $config->soapServerHost,
                        $config->credentials
                    )
                )
            )
        );
    }

    public function createOrUpdateOptionLists(): void
    {
        $this->service->createEnrolmentConfiguration(
            Ports\IncomingMessages\CreateEnrolmentConfiguration::new()
        );
    }

}