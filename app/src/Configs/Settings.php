<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Configs;

use  UnibeEco\EnrolmentOrchestratorOrbital\Adapters;

final readonly class Settings
{

    private function __construct(
        public JsonFilesSettings                    $jsonFilesSettings,
        public Adapters\QueryAdapter\Configs\Config $queryAdapterConfig,
    )
    {

    }

    public static function new(
        Evnvironments $evnvironments
    ): self
    {
        return new self(
            $evnvironments->jsonFilesSettings,
            Adapters\QueryAdapter\Configs\Config::new(
                Adapters\QueryAdapter\Schemas\ReadActionSchemas::new(),
                Adapters\QueryAdapter\Configs\Settings::new(
                    SoapServerSettings::new(
                        $evnvironments->soapServerWsdlProtocol,
                        $evnvironments->soapServerPort,
                        $evnvironments->soapServerHost,
                        $evnvironments->soapUser,
                        $evnvironments->soapPassword,
                    ),
                    $evnvironments->omnitrackerServerHost
                )
            )
        );
    }
}