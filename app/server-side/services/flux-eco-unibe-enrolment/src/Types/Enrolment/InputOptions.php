<?php

namespace FluxEco\UnibeEnrolment\Types\Enrolment;

use FluxEcoType\FluxEcoDefinitionItem;
use FluxEcoType\FluxEcoFilePathDefinition;

final readonly class InputOptions
{
    private function __construct(
        public FluxEcoDefinitionItem $salutations
    )
    {

    }

    public static function new(
        string $inputOptionsDirectory
    ): self
    {
        return new self(
            FluxEcoDefinitionItem::new("salutations", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "salutations.json")),
        );
    }
}