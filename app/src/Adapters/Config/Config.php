<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class Config
{
    public string $valueObjectsConfigDirectoryPath;
    public string $pageObjectDirectoryPath;

    public array $degreeProgramSubjectFilter;

    private function __construct(
        public string          $configFilesDirectoryPath,
        public string                   $soapServerHost,
        public ValueObjects\Server      $soapWsdlServer,
        public ValueObjects\Credentials $credentials
    )
    {
        $this->valueObjectsConfigDirectoryPath = $configFilesDirectoryPath."/value-objects";
        $this->pageObjectDirectoryPath = $configFilesDirectoryPath."/page-objects";

        $degreeProgramSubjectFilter[] = DegreeProgramConfig::new(
            745096, [15,16]
        ); //bachelor
        $degreeProgramSubjectFilter[] = DegreeProgramConfig::new(
            745097, [25]
        ); //master
        $degreeProgramSubjectFilter[] = DegreeProgramConfig::new(
            745099, [56]
        ); //minormob bachelor

        $this->degreeProgramSubjectFilter = $degreeProgramSubjectFilter;
    }

    public static function new(): self
    {
        return new self(
            EnvName::FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_CONFIG_FILES_DIRECTORY_PATH->toConfigValue(),
            EnvName::FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_SERVER_HOST_FILE->toConfigValue(),
            ValueObjects\Server::new(
                EnvName::FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_PROTOCOL->toConfigValue(),
                EnvName::FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_HOST_FILE->toConfigValue(),
                EnvName::FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_PORT->toConfigValue(),
            ),
            ValueObjects\Credentials::new(
                EnvName::FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_USER_FILE->toConfigValue(),
                EnvName::FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_PASSWORD_FILE->toConfigValue(),
            )
        );
    }
}