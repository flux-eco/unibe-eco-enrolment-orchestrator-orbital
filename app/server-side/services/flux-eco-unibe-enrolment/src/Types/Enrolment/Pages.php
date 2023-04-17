<?php

namespace FluxEco\UnibeEnrolment\Types\Enrolment;


use FluxEcoType\FluxEcoFilePathDefinition;
use FluxEcoType\FluxEcoDefinitionItem;

final class Pages
{
    private array $pages;

    private function __construct(
        public readonly FluxEcoDefinitionItem $create,
        public readonly FluxEcoDefinitionItem $identificationNumber,
        public readonly FluxEcoDefinitionItem $choiceSubject,
        public readonly FluxEcoDefinitionItem $intendedDegreeProgram,
        public readonly FluxEcoDefinitionItem $intendedDegreeProgram2,
        public readonly FluxEcoDefinitionItem $universityEntranceQualification,
        public readonly FluxEcoDefinitionItem $portrait,
        public readonly FluxEcoDefinitionItem $personalData,
        public readonly FluxEcoDefinitionItem $legal,
        public readonly FluxEcoDefinitionItem $completed,
        public readonly FluxEcoDefinitionItem $resume
    )
    {
        foreach (get_object_vars($this) as /** @var FluxEcoDefinitionItem $page */ $page) {
            $this->pages[$page->name] = $page;
        }
    }

    public function getPageByName(string $pageName)
    {
        return $this->pages[$pageName];
    }

    public static function new(
        string $pageItemsDirectory
    ): self
    {
        //todo either we write page names like defined here to the json file or we read the name from the json file. we should not have two places where the name is initial defined.
        return new self(
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
            FluxEcoDefinitionItem::new("resume", FluxEcoFilePathDefinition::new($pageItemsDirectory, "resume.json")),
        );
    }
}