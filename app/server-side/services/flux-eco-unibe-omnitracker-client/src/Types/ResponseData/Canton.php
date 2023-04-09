<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\ResponseData;

final readonly class Canton
{
    /**
     * @param string $id
     * @param Label $label
     */
    private function __construct(
        public string $id,
        public Label $label
    )
    {

    }

    /**
     * @param string $id
     * @param Label $label
     * @return static
     */
    public static function new(
        string $id,
        Label $label
    ): self
    {
        return new self($id, $label);
    }
}