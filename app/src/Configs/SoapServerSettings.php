<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Configs;

use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Types;

final readonly class SoapServerSettings implements Types\SoapServerSettings
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

    /**
     * @return string
     */
    public function getSoapServerWsdlProtocol(): string
    {
        return $this->soapServerWsdlProtocol;
    }

    /**
     * @return string
     */
    public function getSoapServerPort(): string
    {
        return $this->soapServerPort;
    }

    /**
     * @return string
     */
    public function getSoapServerHost(): string
    {
        return $this->soapServerHost;
    }

    /**
     * @return string
     */
    public function getSoapUser(): string
    {
        return $this->soapUser;
    }

    /**
     * @return string
     */
    public function getSoapPassword(): string
    {
        return $this->soapPassword;
    }



    public function toString(): string
    {
        return sprintf('%s://%s:%s', $this->soapServerWsdlProtocol, $this->soapServerHost, $this->soapServerPort);
    }
}