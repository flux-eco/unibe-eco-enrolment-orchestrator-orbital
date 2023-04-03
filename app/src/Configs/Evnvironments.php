<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Configs;

final readonly class Evnvironments
{

    private function __construct(
        public JsonFilesSettings $jsonFilesSettings,
        public string            $soapServerWsdlProtocol,
        public string            $soapServerPort,
        public string            $soapServerHost,
        public string            $soapUser,
        public string            $soapPassword,
        public string            $omnitrackerServerHost,
    )
    {

    }

    public static function new(): self
    {
        //todo

        return new self(
            JsonFilesSettings::new(
                "/opt/unibe-eco-enrolment-orchestrator-orbital/configs/reference-objects",
                "/opt/unibe-eco-enrolment-orchestrator-orbital/configs/page-objects",
            ),
            getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_PROTOCOL') ?? "https",
            getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_PORT') ?? "443",
            self::readConfigFile(getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_HOST_FILE')) ?? self::readConfigFile("../../secrets/soap-wsdl-server-host"),
            self::readConfigFile(getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_USER_FILE')) ?? self::readConfigFile("../../secrets/soap-user"),
            self::readConfigFile(getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_PASSWORD_FILE')) ?? self::readConfigFile("../../secrets/soap-password"),
            self::readConfigFile(getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_SERVER_HOST_FILE')) ?? self::readConfigFile("../../secrets/soap-server-host")
        );
    }

    static function readConfigFile(string $filePath): string|false
    {
        return file_get_contents(str_replace(["\r", "\n"], '', $filePath));
    }

}