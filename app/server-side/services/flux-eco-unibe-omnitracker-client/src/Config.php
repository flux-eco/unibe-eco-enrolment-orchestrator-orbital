<?php

namespace FluxEco\UnibeOmnitrackerClient;

use FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi\BaseDataItemAttributesDefinition;
use FluxEcoType\FluxEcoBasicCredentials;
use FluxEcoType\FluxEcoServerBindingDefinition;
use stdClass;

final readonly class Config
{
    public function __construct(
        public BaseDataItemAttributesDefinition $baseDataItemDefinition,
        public Types\Settings        $settings
    )
    {

    }

    public static function new(): Config
    {
        $baseDataItemAttributesDefinition = BaseDataItemAttributesDefinition::new();

        return new self(
            BaseDataItemAttributesDefinition::new(),
            Types\Settings::new(
                FluxEcoServerBindingDefinition::new(
                    getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_PROTOCOL') ?? "https",
                    getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_PORT') ?? "443",
                    self::readConfigFile(getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_WSDL_SERVER_HOST_FILE')) ?? self::readConfigFile("../../secrets/soap-wsdl-server-host"),
                ),
                FluxEcoBasicCredentials::new(
                    self::readConfigFile(getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_USER_FILE')) ?? self::readConfigFile("../../secrets/soap-user"),
                    self::readConfigFile(getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_PASSWORD_FILE')) ?? self::readConfigFile("../../secrets/soap-password"),
                ),
                self::readConfigFile(getenv('FLUX_ECO_ENROLMENT_ORCHESTRATOR_ORBITAL_SOAP_SERVER_HOST_FILE')) ?? self::readConfigFile("../../secrets/soap-server-host"),
                $baseDataItemAttributesDefinition,
                'de'
            )
        );
    }

    public function createAbsoluteActionPath(string $serverPath, string $relativeActionPath): string
    {
        return sprintf('%s/%s', $serverPath, $relativeActionPath);
    }

    static function readConfigFile(string $filePath): string|false
    {
        return file_get_contents(str_replace(["\r", "\n"], '', $filePath));
    }
}

