<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\LanguageCode;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\Repositories\EnrolmentRepository;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\ConfigurationObjectName;

final class EnrolmentAggregate
{
    private string $sessionId;
    private ?ValueObjects\EnrolmentData $enrolmentData;

    private function __construct(
        public MessageRecorder      $messageRecorder,
        private EnrolmentRepository $enrolmentRepository
    )
    {
        $this->sessionId = "";
        $this->enrolmentData = null;
    }

    public static function new(
        MessageRecorder     $messageRecorder,
        EnrolmentRepository $enrolmentRepository
    ): self
    {
        return new self($messageRecorder, $enrolmentRepository);
    }

    public function provideLayout(string $valueObjectConfigDirectoryPath): void
    {
        $headers = OutgoingMessages\MessageHeaders::new(OutgoingMessages\MessageName::PROVIDE_CONFIGURATION_OBJECT->value, OutgoingMessages\MessageName::PROVIDE_CONFIGURATION_OBJECT->toAddress());
        $this->messageRecorder->record(OutgoingMessages\Message::new($headers, ConfigurationObjectName::LAYOUT->toObject($valueObjectConfigDirectoryPath)));
    }

    public function providePage(string $pageObjectDirectoryPath, ValueObjects\PageName $pageName, ?ValueObjects\EnrolmentData $enrolmentData): void
    {
        $headers = OutgoingMessages\MessageHeaders::new(OutgoingMessages\MessageName::PUBLISH_PAGE_OBJECT->value, OutgoingMessages\MessageName::PUBLISH_PAGE_OBJECT->toAddress());
        $pageObject = $this->resolveReferences($this->getNextPage($pageName)->toObject($pageObjectDirectoryPath), $pageObjectDirectoryPath);
        $pageObject = $this->hydratePageData($pageObject, $enrolmentData);

        match ($this->getNextPage($pageName)) {
            ValueObjects\PageName::INTENDED_DEGREE_PROGRAM => $pageObject = $this->hydrateSubjectData($pageObject, $enrolmentData),
            ValueObjects\PageName::INTENDED_DEGREE_PROGRAM_2 => $pageObject = $this->hydrateChoosenSubjectData($pageObject, $enrolmentData),
            default => []
        };
        $this->messageRecorder->record(OutgoingMessages\Message::new($headers, $pageObject));
    }

    private function getNextPage(ValueObjects\PageName $lastPage)
    {
        return match ($lastPage) {
            ValueObjects\PageName::CREATE => ValueObjects\PageName::IDENTIFICATION_NUMBER,
            ValueObjects\PageName::START => ValueObjects\PageName::START,
            ValueObjects\PageName::IDENTIFICATION_NUMBER => ValueObjects\PageName::CHOICE_SUBJECT,
            ValueObjects\PageName::CHOICE_SUBJECT => ValueObjects\PageName::INTENDED_DEGREE_PROGRAM,
            ValueObjects\PageName::INTENDED_DEGREE_PROGRAM => ValueObjects\PageName::INTENDED_DEGREE_PROGRAM_2,
            ValueObjects\PageName::INTENDED_DEGREE_PROGRAM_2 => ValueObjects\PageName::UNIVERSITY_ENTRANCE_QUALIFICATION,
            ValueObjects\PageName::UNIVERSITY_ENTRANCE_QUALIFICATION => ValueObjects\PageName::PORTRAIT,
            ValueObjects\PageName::PORTRAIT => ValueObjects\PageName::PAGE_DATA,
            ValueObjects\PageName::PAGE_DATA => [],
            ValueObjects\PageName::LEGAL => [],
            ValueObjects\PageName::COMPLETED => []
        };
    }

    public function storeData(ValueObjects\PageName $pageName, string $sessionId, object $dataToStore, ValueObjects\EnrolmentData $enrolmentData): void
    {
        $this->enrolmentData = $enrolmentData;
        match ($pageName) {
            ValueObjects\PageName::CREATE => $this->createEnrolment($sessionId, $dataToStore->semester, $dataToStore->password),
            ValueObjects\PageName::START => throw new \Exception('To be implemented'),
            ValueObjects\PageName::IDENTIFICATION_NUMBER => $this->applyDataStored(OutgoingMessages\DataStored::new(ValueObjects\PageName::IDENTIFICATION_NUMBER, $sessionId, $enrolmentData, true)),
            ValueObjects\PageName::CHOICE_SUBJECT => $this->changeChoiceSubject($sessionId, $dataToStore->{'degree-program'}, $dataToStore->{'qualifications'}),
            ValueObjects\PageName::INTENDED_DEGREE_PROGRAM => $this->changeDegreeProgram($sessionId, $dataToStore->{'subject'}, $dataToStore->{'combination'}),
            ValueObjects\PageName::INTENDED_DEGREE_PROGRAM_2 => $this->changeDegreeProgramDetail($sessionId, $dataToStore->{'single-choice'}, $dataToStore->{'multiple-choice'}, $dataToStore->{'further-information'}),
            ValueObjects\PageName::UNIVERSITY_ENTRANCE_QUALIFICATION => $this->changeEntranceQualification($sessionId, $dataToStore->{'certificate-type'}),
            ValueObjects\PageName::PORTRAIT => $this->changePortrait($sessionId),
            ValueObjects\PageName::PAGE_DATA => throw new \Exception('To be implemented'),
            ValueObjects\PageName::LEGAL => throw new \Exception('To be implemented'),
            ValueObjects\PageName::COMPLETED => throw new \Exception('To be implemented')
        };
    }

    private function createEnrolment(string $sessionId, string $semester, string $password)
    {
        $storedBaseData = $this->enrolmentRepository->create($sessionId, $password, Enums\LanguageCode::DE);
        $this->applyEnrolmentCreated(OutgoingMessages\EnrolmentCreated::new(ValueObjects\EnrolmentData::new($storedBaseData)));
        $this->applyDataStored(OutgoingMessages\DataStored::new(ValueObjects\PageName::CREATE, $sessionId, ValueObjects\EnrolmentData::new($storedBaseData), true));
    }

    private function applyEnrolmentCreated(OutgoingMessages\EnrolmentCreated $enrolmentCreated)
    {
        $cookies = [
            ValueObjects\ValueObjectName::IDENTIFICATION_NUMBER->toParameterName() => $enrolmentCreated->data->baseData->Identifikationsnummer
        ];
        $headers = OutgoingMessages\MessageHeaders::new(OutgoingMessages\MessageName::ENROLMENT_CREATED->value, OutgoingMessages\MessageName::ENROLMENT_CREATED->toAddress([ValueObjects\ValueObjectName::IDENTIFICATION_NUMBER->toParameterString($enrolmentCreated->data->baseData->Identifikationsnummer)]), $cookies);
        $message = OutgoingMessages\Message::new($headers, $enrolmentCreated);
        $this->messageRecorder->record($message);
    }

    private function changeChoiceSubject(string $sessionId, string $degreeProgram, object $qualifications)
    {
        $baseData = $this->enrolmentData->baseData;
        $baseData->StudienstufeUniqueId = $degreeProgram;
        $storedBaseData = $this->enrolmentRepository->storeBaseData($sessionId, $baseData, Enums\LanguageCode::DE);

        $this->applyDataStored(OutgoingMessages\DataStored::new(ValueObjects\PageName::CHOICE_SUBJECT, $sessionId, ValueObjects\EnrolmentData::new($storedBaseData), true));
    }

    private function changeDegreeProgram(string $sessionId, int $subject, int $combination)
    {
        $baseData = $this->enrolmentData->baseData;
        $baseData->StudiengangsversionUniqueId = $subject;
        $baseData->StudienstrukturUniqueId = $combination;
        $storedBaseData = $this->enrolmentRepository->storeBaseData($sessionId, $baseData, Enums\LanguageCode::DE);

        $this->applyDataStored(OutgoingMessages\DataStored::new(ValueObjects\PageName::INTENDED_DEGREE_PROGRAM, $sessionId, ValueObjects\EnrolmentData::new($storedBaseData), true));
    }

    private function changeDegreeProgramDetail(string $sessionId, ?object $singleChoice, ?object $multipleChoice, string $furtherInformation)
    {
        $baseData = $this->enrolmentData->baseData;
        if ($singleChoice !== null) {
            //todo foreach ->
            /*
             * pStudienprogrammUniqueId
             */
        }
        if ($multipleChoice !== null) {
            //todo foreach ->
            /*
             * pStudienprogrammUniqueId
             */
        }
        if ($furtherInformation !== null) {

        }

        $this->applyDataStored(OutgoingMessages\DataStored::new(ValueObjects\PageName::INTENDED_DEGREE_PROGRAM_2, $sessionId, ValueObjects\EnrolmentData::new($baseData), true));
    }

    private function changeEntranceQualification(string $sessionId, int $certificateType)
    {
        $baseData = $this->enrolmentData->baseData;
        //

        $this->applyDataStored(OutgoingMessages\DataStored::new(ValueObjects\PageName::UNIVERSITY_ENTRANCE_QUALIFICATION, $sessionId, ValueObjects\EnrolmentData::new($baseData), true));

    }

    private function changePortrait(string $sessionId)
    {
        $baseData = $this->enrolmentData->baseData;
        //
        $this->applyDataStored(OutgoingMessages\DataStored::new(ValueObjects\PageName::PORTRAIT, $sessionId, ValueObjects\EnrolmentData::new($baseData), true));

    }


    private function applyDataStored(OutgoingMessages\DataStored $dataStored)
    {
        $this->enrolmentData = $dataStored->enrolmentData;
        $cookies = [
            ValueObjects\ValueObjectName::ENROLMENT_DATA->toParameterName() => json_encode($dataStored->enrolmentData),
            ValueObjects\ValueObjectName::SESSION_ID->toParameterName() => $dataStored->sessionId,
            ValueObjects\ValueObjectName::CURRENT_PAGE->toParameterName() => $dataStored->pageName->value
        ];
        $headers = OutgoingMessages\MessageHeaders::new(OutgoingMessages\MessageName::DATA_STORED->value, OutgoingMessages\MessageName::DATA_STORED->toAddress(), $cookies);
        $message = OutgoingMessages\Message::new($headers, $dataStored);
        $this->messageRecorder->record($message);
    }

    private function resolveReferences(object $pageObject, string $pageObjectDirectoryPath): object
    {
        foreach ($pageObject->data as $keyName => $value) {
            if ($keyName === '$ref') {
                $pageObject->data = json_decode(file_get_contents($pageObjectDirectoryPath . "/" . $value));
            }
            if (is_object($value) && property_exists($value, '$ref')) {
                $pageObject->data->$keyName = json_decode(file_get_contents($pageObjectDirectoryPath . "/" . $value->{'$ref'}));
            }
            if ($keyName === "data") {

                if (is_array($value)) {
                    $data = [];
                    foreach ($value as $key => $item) {
                        if (is_object(($item)) && property_exists($item, '$ref')) {
                            $data[] = json_decode(file_get_contents($pageObjectDirectoryPath . "/" . $item->{'$ref'}));
                        }
                    }
                    $pageObject->data->data = $data;
                }
            }
        }
        return $pageObject;
    }

    private function hydratePageData(object $pageObject, ?ValueObjects\EnrolmentData $enrolmentData): object
    {
        if ($enrolmentData !== null) {
            $data = $pageObject->data;
            foreach ($enrolmentData->toFrontendObject() as $key => $value) {
                $data->{$key} = $value;
            }
        }
        $pageObject->data = $data;
        return $pageObject;
    }

    private function hydrateSubjectData(object $pageObject, ?ValueObjects\EnrolmentData $enrolmentData): object
    {
        $pageObject->data->subjects = $pageObject->data->subjects->{$enrolmentData->baseData->StudienstufeUniqueId};
        return $pageObject;
    }

    private function hydrateChoosenSubjectData(object $pageObject, ?ValueObjects\EnrolmentData $enrolmentData): object
    {
        $pageObject->data->subject = Entities\Subject::new(
            $enrolmentData->baseData->StudiengangsversionUniqueId,
            ValueObjects\Label::new(
                [
                    ValueObjects\LocalizedStringValue::new(
                        LanguageCode::DE->value,
                        $enrolmentData->baseData->Studiengangsversion
                    ),
                    ValueObjects\LocalizedStringValue::new(
                        LanguageCode::EN->value,
                        $enrolmentData->baseData->Studiengangsversion
                    )
                ]),
            $enrolmentData->baseData->StudiengangsversionReqEcts,
        );
        $pageObject->data->combination = $pageObject->data->combinations->{$enrolmentData->baseData->StudienstrukturUniqueId};

        return $pageObject;
    }


    /*
     public function applyPageProvided(OutgoingMessages\ProvideReferenceObject $payload): void
    {
        $this->messageRecorder->record(OutgoingMessages\Message::new($headers, $payload));
    }
    */


    /* public function storeData(ValueObjects\PageType $pageType, object $data)
     {
         match ($pageType) {
             ValueObjects\PageType::CREATE => $this->create($data->password),
             default => $this->applyDataStored(Events\DataStored::new(true, [], ['last-page' => $task->pageType->value]))
         };
     }*/


}