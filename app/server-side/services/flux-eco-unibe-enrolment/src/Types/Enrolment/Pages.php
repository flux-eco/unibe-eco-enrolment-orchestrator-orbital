<?php

namespace FluxEco\UnibeEnrolment\Types\Enrolment;



use FluxEcoType\FluxEcoFilePathDefinition;
use FluxEcoType\FluxEcoDefinitionItem;

final readonly class Pages
{
    private function __construct(
        public FluxEcoDefinitionItem $start,
        public FluxEcoDefinitionItem $create,
        public FluxEcoDefinitionItem $identificationNumber,
        public FluxEcoDefinitionItem $choiceSubject,
        public FluxEcoDefinitionItem $intendedDegreeProgram,
        public FluxEcoDefinitionItem $intendedDegreeProgram2,
        public FluxEcoDefinitionItem $universityEntranceQualification,
        public FluxEcoDefinitionItem $portrait,
        public FluxEcoDefinitionItem $personalData,
        public FluxEcoDefinitionItem $legal,
        public FluxEcoDefinitionItem $completed
    )
    {

    }

    public static function new(
        string $pageItemsDirectory
    ): self
    {
        //todo either we write page names like defined here to the json file or we read the name from the json file. we should not have two places where the name is initial defined.
        return new self(
            FluxEcoDefinitionItem::new("start", FluxEcoFilePathDefinition::new($pageItemsDirectory, "start.json")),
            FluxEcoDefinitionItem::new("create", FluxEcoFilePathDefinition::new($pageItemsDirectory, "create.json")),
            FluxEcoDefinitionItem::new("identification-number", FluxEcoFilePathDefinition::new($pageItemsDirectory, "identification-number.json")),
            FluxEcoDefinitionItem::new("choice-subject", FluxEcoFilePathDefinition::new($pageItemsDirectory, "choice-subject.json")),
            FluxEcoDefinitionItem::new("intended-degree-program", FluxEcoFilePathDefinition::new($pageItemsDirectory, "intended-degree-program.json")),
            FluxEcoDefinitionItem::new("intended-degree-program-2", FluxEcoFilePathDefinition::new($pageItemsDirectory, "intended-degree-program-2.json")),
            FluxEcoDefinitionItem::new("university-entrance-qualification", FluxEcoFilePathDefinition::new($pageItemsDirectory, "university-entrance-qualification.json")),
            FluxEcoDefinitionItem::new("portrait", FluxEcoFilePathDefinition::new($pageItemsDirectory, "portrait.json")),
            FluxEcoDefinitionItem::new("personal-data", FluxEcoFilePathDefinition::new($pageItemsDirectory, "personal-data.json")),
            FluxEcoDefinitionItem::new("legal", FluxEcoFilePathDefinition::new($pageItemsDirectory, "legal.json")),
            FluxEcoDefinitionItem::new("completed", FluxEcoFilePathDefinition::new($pageItemsDirectory, "completed.json")),
        );
    }
}