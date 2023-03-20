<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;


final readonly class SelectLabelInputOption
{
    private function __construct(
        public string   $id,
        public Label $label
    )
    {

    }

    public static function new(
        string   $id,
        Label $label
    ): self
    {
        return new self(...get_defined_vars());
    }
}