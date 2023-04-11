<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\ResponseData;


final readonly class Subject {
    private function __construct(
        public string $id,
        public Label $label,
        public int $ect,
        public array $combinations = []
    ) {

    }

    public static function new(
        string $id,
        Label $label,
        int $ect,
        array $combinations = []
    ) {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize() : mixed
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'ect' => $this->ect,
            'combinations' => $this->combinations
        ];
    }
}