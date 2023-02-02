<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class Server
{
    private function __construct(
        public string $protocol,
        public string $host,
        public string $port
    ) {

    }

    public static function new(
        string $protocol,
        string $host,
        string $port
    ) : self {
        return new self(...get_defined_vars());
    }

    public function toString() : string
    {
        return $this->protocol . "://" . $this->host . ":" . $this->port;
    }
}