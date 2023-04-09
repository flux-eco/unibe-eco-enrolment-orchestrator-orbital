<?php

namespace FluxEco\UnibeEnrolment\Types\Enrolment;

use FluxEco\UnibeEnrolment\Types\DataDirectories;
use FluxEcoType\FluxEcoWorkflowDefinitionNextPageName;
use FluxEcoType\Workflow;

final readonly class EnrolmentDefinition
{
    public Workflow $workflow;

    private function __construct(
        public Pages            $pages,
        public InputOptions     $inputOptions,
        public OutputDataObject $outputDataObject
    )
    {
        $this->workflow = Workflow::new(
            $pages->start->name,
            [
                $pages->create->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->identificationNumber->name),
                $pages->identificationNumber->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->choiceSubject->name),
                $pages->choiceSubject->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->identificationNumber->name),
                $pages->identificationNumber->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->intendedDegreeProgram2->name),
                $pages->intendedDegreeProgram2->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->universityEntranceQualification->name),
                $pages->universityEntranceQualification->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->portrait->name),
                $pages->portrait->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->personalData->name),
                $pages->personalData->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->legal->name),
                $pages->legal->name => FluxEcoWorkflowDefinitionNextPageName::new($pages->completed->name)
            ]
        );
    }

    public static function new(
        DataDirectories $dataDirectories
    )
    {
        return new self(
            Pages::new($dataDirectories->pageDefinitionStates),
            InputOptions::new($dataDirectories->inputOptionsDefinitionStates),
            OutputDataObject::new()
        );
    }

}