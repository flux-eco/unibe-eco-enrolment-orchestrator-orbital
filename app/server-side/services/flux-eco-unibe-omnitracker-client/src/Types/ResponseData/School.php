<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\ResponseData;

final readonly class School
{
    private function __construct(
        public string $id,
        public Label  $label,
        public string $schoolTypeId,
        public string $certificateId,
        public string $cantonId
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
        Label  $label,
        string $schoolTypeId,
        string $certificateId,
        string $cantonId
    ): self
    {
        return new self($id, $label, $schoolTypeId, $certificateId, $cantonId);
    }
}