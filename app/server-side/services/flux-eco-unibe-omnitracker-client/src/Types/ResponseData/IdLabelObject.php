<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\ResponseData;

final readonly class IdLabelObject
{
    private function __construct(
        public string $id,
        public Label  $label,
    )
    {

    }

    public static function new(
        string $id,
        Label  $label,
    ): self
    {
        return new self($id, $label);
    }
}