<?php

namespace FluxEco\UnibeEnrolment\Types\Enrolment;

use FluxEco\UnibeEnrolment\Types\DataDirectories;
use FluxEcoType\FluxEcoDefinitionItem;
use FluxEcoType\FluxEcoFilePathDefinition;
use FluxEcoType\FluxEcoWorkflowDefinitionNextPageAsPropertyName;
use FluxEcoType\Workflow;

final readonly class EnrolmentDefinition
{
    public Workflow $workflow;

    private function __construct(
        public FluxEcoDefinitionItem $layout,
        public Pages                 $pages,
        public InputOptions          $inputOptions,
        public WorkflowOutputDefinition $outputDataObjectDefinition
    )
    {
        $this->workflow = Workflow::new(
            $pages->start->name,
            [
                $pages->create->name => FluxEcoWorkflowDefinitionNextPageAsPropertyName::new($pages->identificationNumber->asPropertyName),
                $pages->identificationNumber->name => FluxEcoWorkflowDefinitionNextPageAsPropertyName::new($pages->choiceSubject->asPropertyName),
                $pages->choiceSubject->name => FluxEcoWorkflowDefinitionNextPageAsPropertyName::new($pages->intendedDegreeProgram->asPropertyName),
                $pages->intendedDegreeProgram->name => FluxEcoWorkflowDefinitionNextPageAsPropertyName::new($pages->intendedDegreeProgram2->asPropertyName),
                $pages->intendedDegreeProgram2->name => FluxEcoWorkflowDefinitionNextPageAsPropertyName::new($pages->universityEntranceQualification->asPropertyName),
                $pages->universityEntranceQualification->name => FluxEcoWorkflowDefinitionNextPageAsPropertyName::new($pages->portrait->asPropertyName),
                $pages->portrait->name => FluxEcoWorkflowDefinitionNextPageAsPropertyName::new($pages->personalData->asPropertyName),
                $pages->personalData->name => FluxEcoWorkflowDefinitionNextPageAsPropertyName::new($pages->legal->asPropertyName),
                $pages->legal->name => FluxEcoWorkflowDefinitionNextPageAsPropertyName::new($pages->completed->asPropertyName)
            ]
        );
    }

    public static function new(
        DataDirectories $dataDirectories
    )
    {
        return new self(
            FluxEcoDefinitionItem::new(
                "layout",
                FluxEcoFilePathDefinition::new(
                    $dataDirectories->layoutDefinitionStates,
                    "layout.json"
                )
            ),
            Pages::new($dataDirectories->pageDefinitionStates),
            InputOptions::new($dataDirectories->inputOptionsDefinitionStates),
            WorkflowOutputDefinition::new()
        );
    }

}