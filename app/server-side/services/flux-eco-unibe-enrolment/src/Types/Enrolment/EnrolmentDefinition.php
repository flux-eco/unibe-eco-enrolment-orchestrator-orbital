<?php

namespace FluxEco\UnibeEnrolment\Types\Enrolment;

use FluxEco\UnibeEnrolment\Types\DataDirectories;
use FluxEcoType\FluxEcoDefinitionItem;
use FluxEcoType\FluxEcoFilePathDefinition;
use FluxEcoType\FluxEcoWorkflowDefinitionNextPageName;
use FluxEcoType\Workflow;


final readonly class EnrolmentDefinition
{
    public Workflow $workflow;

    private function __construct(
        public FluxEcoDefinitionItem    $layout,
        public Pages                    $pages,
        public InputOptions             $inputOptions,
        public WorkflowOutputDefinition $outputDataObjectDefinition
    )
    {
        $this->workflow = Workflow::new(
            $pages->create->name,
            [
                $pages->create->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->identificationNumber->name),
                $pages->identificationNumber->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->choiceSubject->name),
                $pages->choiceSubject->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->intendedDegreeProgram->name),
                $pages->intendedDegreeProgram->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->intendedDegreeProgram2->name),
                $pages->intendedDegreeProgram2->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->universityEntranceQualification->name),
                $pages->universityEntranceQualification->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->portrait->name),
                $pages->portrait->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->personalData->name),
                $pages->personalData->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->legal->name),
                $pages->legal->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->completed->name)
            ],
            $pages->completed->name,
            $pages->resume->name,
            'LastCompletedController'
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