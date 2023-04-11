<?php

namespace FluxEco\UnibeEnrolment\Types\Enrolment;

use FluxEcoType\FluxEcoDefinitionItem;
use FluxEcoType\FluxEcoFilePathDefinition;

final readonly class InputOptions
{
    private function __construct(
        public FluxEcoDefinitionItem $salutations,
        public FluxEcoDefinitionItem $semesters,
        public FluxEcoDefinitionItem $subjects,
        public FluxEcoDefinitionItem $subjectCombinations,
        public FluxEcoDefinitionItem $places,
        public FluxEcoDefinitionItem $motherLanguage,
        public FluxEcoDefinitionItem $correspondenceLanguage
    )
    {

    }

    public static function new(
        string $inputOptionsDirectory
    ): self
    {
        return new self(
            FluxEcoDefinitionItem::new("salutations", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "salutations.json")),
            FluxEcoDefinitionItem::new("semesters", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "semesters.json")),
            FluxEcoDefinitionItem::new("subjects", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "subjects.json")),
            FluxEcoDefinitionItem::new("subject-combinations", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "subject-combinations.json")),
            FluxEcoDefinitionItem::new("places", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "places.json")),
            FluxEcoDefinitionItem::new("motherLanguage", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "motherLanguage.json")),
            FluxEcoDefinitionItem::new("correspondenceLanguage", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "correspondenceLanguage.json")),
        );
    }
}