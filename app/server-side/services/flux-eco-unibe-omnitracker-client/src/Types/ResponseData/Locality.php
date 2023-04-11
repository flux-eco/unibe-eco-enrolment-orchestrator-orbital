<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\ResponseData;

use JsonSerializable;

class Locality implements JsonSerializable
{
    /**
     * @param string $id
     * @param Label $label
     * @param int $plz
     * @param string $cantonId
     */
    private function __construct(
        public string $id,
        public Label  $label,
        public int    $plz,
        public string $cantonId
    )
    {

    }

    /**
     * @param string $id
     * @param Label $label
     * @param int $plz
     * @param string $cantonId
     * @return static
     */
    public static function new(
        string $id,
        Label  $label,
        int    $plz,
        string $cantonId
    ): self
    {
        return new self($id, $label, $plz, $cantonId);
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id,
            "postal-code" => $this->plz,
            "label" => $this->label
        ];
    }
}