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
        public FluxEcoDefinitionItem $originPlaces,
        public FluxEcoDefinitionItem $motherLanguages,
        public FluxEcoDefinitionItem $correspondenceLanguages,
        public FluxEcoDefinitionItem $countries,
        public FluxEcoDefinitionItem $areaCodes,
        public FluxEcoDefinitionItem $nationalities
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
            FluxEcoDefinitionItem::new("origin-places", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "origin-places.json")),
            FluxEcoDefinitionItem::new("mother-languages", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "mother-languages.json")),
            FluxEcoDefinitionItem::new("correspondence-languages", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "correspondence-languages.json")),
            FluxEcoDefinitionItem::new("countries", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "countries.json")),
            FluxEcoDefinitionItem::new("area-codes", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "area-codes.json")),
            FluxEcoDefinitionItem::new("nationalities", FluxEcoFilePathDefinition::new($inputOptionsDirectory, "nationalities.json")),
        );
    }
}