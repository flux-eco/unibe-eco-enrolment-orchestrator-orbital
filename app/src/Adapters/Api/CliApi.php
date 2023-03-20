<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Api;

use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\OmnitrackerBinding;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Omnitracker\OmnitrackerHelpTableRecords;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\InputType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports;
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
                    $config->configFilesDirectoryPath,
                    Repositories\EnrolmentConfigurationReferenceObjectRepository::new(
                        $config->configFilesDirectoryPath,
                        $config->soapWsdlServer,
                        $config->soapServerHost,
                        $config->credentials,
                        $config->degreeProgramSubjectFilter
                    ),
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
        $helpTableRecords = OmnitrackerHelpTableRecords::new(
            OmnitrackerBinding::new(
                $this->config->soapServerHost,
                $this->config->soapWsdlServer,
                $this->config->credentials
            ),
            $this->config->configFilesDirectoryPath
        );


        $helpTableRecords->writeReferenceObject(ObjectType::CERTIFICATE_TYPES->value, $helpTableRecords->getCertificateTypeSelectInputOptions());
        $helpTableRecords->writeReferenceObject(ObjectType::CERTIFICATES_ISSUE_YEARS->value, $helpTableRecords->getIssueYearSelectInputOptions());
        $helpTableRecords->writeReferenceObject(ObjectType::CERTIFICATES->value, $helpTableRecords->getCertificateSelectInputOptions());

        $helpTableRecords->writeReferenceObject(ObjectType::UNIVERSITY_QUALIFICATION_SELECTS->value, $helpTableRecords->getUniversityQualificationDependentSelects());

    }

}