<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital;

use Exception;
use UnibeEco\EnrolmentOrchestratorOrbital\Configs;


final readonly class CliApi
{
    private function __construct(
        private Configs\Config $config
    )
    {

    }

    public static function new(): self
    {
        return new self(
            Configs\Config::new(
                Configs\Settings::new(
                    Configs\Evnvironments::new()
                )
            )
        );

        /*
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
        */
    }

    /**
     * @throws Exception
     */
    public function writeJsonFiles(): void
    {
        $this->writeEntranceQualificationJsonFiles();
    }

    /**
     * @throws Exception
     */
    private function writeEntranceQualificationJsonFiles(): void
    {
        $entranceQualificationPage = Pages\UniversityEntranceQualification\Page::new(
            Pages\UniversityEntranceQualification\Configs\Configs::new(
                Pages\UniversityEntranceQualification\Configs\Outbounds::new(
                    Adapters\QueryAdapter\JsonFileReaderAdapter::new(),
                    Adapters\QueryAdapter\RawDataReader::new(
                        Adapters\QueryAdapter\Configs\Config::new(
                            Adapters\QueryAdapter\Schemas\ReadActionSchemas::new(),
                            $this->config->settings->queryAdapterConfig->settings,
                        )
                    ),
                ),
                $this->config->settings->jsonFilesSettings->inputDataJsonFilesDirectoryPath,
                $this->config->settings->jsonFilesSettings->pageStructureJsonFilesDirectoryPath
            )
        );
        $entranceQualificationPage->writeInputJsonFiles();
        $entranceQualificationPage->writeInputJsonFiles();
    }
}