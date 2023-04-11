<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\ResponseData;

final readonly class Choice
{
    private function __construct(
        public string $id,
        public Label  $label,
        public int    $ect
    )
    {

    }

    public static function new(
        string $id,
        Label  $label,
        int    $ect
    ): self
    {
        return new self(...get_defined_vars());
    }
}