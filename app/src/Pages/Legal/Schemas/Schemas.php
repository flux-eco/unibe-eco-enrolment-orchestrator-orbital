<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\Legal\Schemas;

final readonly class Schemas
{
    private function __construct(
        public ActionSchemas $actionSchemas,
        public InputSchemas  $optionListInputSchemas
    )
    {

    }

    public static function new(
        ActionSchemas $actionSchemas,
        InputSchemas  $optionListInputSchemas
    ): self
    {
        return new self(...get_defined_vars());
    }
}