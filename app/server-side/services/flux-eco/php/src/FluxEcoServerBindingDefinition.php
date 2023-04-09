<?php

namespace FluxEcoType;

final readonly class FluxEcoServerBindingDefinition
{
    private function __construct(
        public string $protocol,
        public string $port,
        public string $host
    )
    {

    }

    public static function new(
        string $protocol,
        string $port,
        string $host,
    )
    {
        return new self(
            ...get_defined_vars()
        );
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    public function toString(): string
    {
        return sprintf('%s://%s:%s', $this->protocol, $this->host, $this->port);
    }
}