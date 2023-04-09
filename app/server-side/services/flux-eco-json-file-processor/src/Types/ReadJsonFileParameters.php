<?php

namespace FluxEco\JsonFileProcessor\Types;

final readonly class ReadJsonFileParameters
{
    private function __construct(
        public string $directoryPath, public string $jsonFileName
    )
    {

    }

    public static function new(
        string $directoryPath, string $jsonFileName
    )
    {
        return new self(...get_defined_vars());
    }

    public function toObject(): object
    {
        return json_decode(json_encode($this));
    }

}