<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\ResponseData;

final readonly class CertificateType
{
    /**
     * @param string $id
     * @param Label $label
     * @param bool $municipalityRequired
     */
    private function __construct(
        public string            $id,
        public Label $label,
        public bool              $municipalityRequired,
    )
    {

    }

    /**
     * @param string $id
     * @param Label $label
     * @param bool $municipalityRequired
     * @return static
     */
    public static function new(
        string            $id,
        Label $label,
        bool              $municipalityRequired,
    ): self
    {
        return new self($id, $label, $municipalityRequired);
    }
}