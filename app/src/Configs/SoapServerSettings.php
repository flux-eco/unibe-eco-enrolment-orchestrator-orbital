<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Configs;


final readonly class SoapServerSettings
{
    private function __construct(
        public string $soapServerWsdlProtocol,
        public string $soapServerPort,
        public string $soapServerHost,
        public string $soapUser,
        public string $soapPassword
    )
    {

    }

    public static function new(
        string $soapServerWsdlProtocol,
        string $soapServerPort,
        string $soapServerHost,
        string $soapUser,
        string $soapPassword
    )
    {
        return new self(
            ...get_defined_vars()
        );
    }

    public function toString(): string
    {
        return sprintf('%s://%s:%s', $this->soapServerWsdlProtocol, $this->soapServerHost, $this->soapServerPort);
    }
}