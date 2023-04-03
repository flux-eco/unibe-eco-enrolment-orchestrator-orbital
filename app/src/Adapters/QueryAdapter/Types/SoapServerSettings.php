<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter\Types;

interface SoapServerSettings
{
    public function getSoapServerWsdlProtocol(): string;

    public function getSoapServerPort(): string;

    public function getSoapServerHost(): string;

    public function getSoapUser(): string;

    public function getSoapPassword(): string;
}