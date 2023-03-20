<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain;

use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Repositories\EnrolmentConfigurationReferenceObjectRepository;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities\EntityKey;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\PropertyType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\UniversityEntranceQualificationOption;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\InputType;

final class EnrolmentConfigurationAggregate
{
    private \WeakMap $optionStorage;
    private array $selects = [];

    private function __construct(
        public string                                          $configFilesDirectoryPath,
        public EnrolmentConfigurationReferenceObjectRepository $enrolmentConfigurationReferenceObjectsRepository,
        public MessageRecorder                                 $messageRecorder
    )
    {
        $optionStorage = new \WeakMap();
    }

    public static function new(
        string                                          $configFilesDirectoryPath,
        EnrolmentConfigurationReferenceObjectRepository $enrolmentConfigurationReferenceObjectsRepository,
        MessageRecorder                                 $messageRecorder
    ): self
    {
        return new self($configFilesDirectoryPath, $enrolmentConfigurationReferenceObjectsRepository, $messageRecorder);
    }

    public function createOrUpdateSpecification(): void
    {
        $header = OutgoingMessages\MessageHeaders::new(
            OutgoingMessages\MessageName::CREATE_REFERENCE_OBJECT->value,
            OutgoingMessages\MessageName::CREATE_REFERENCE_OBJECT->toAddress([]),
        );
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ObjectType::SUBJECTS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ObjectType::DEGREE_PROGRAMS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ObjectType::SEMESTERS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ObjectType::GRADUATION_TYPES)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ObjectType::PLACES)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ObjectType::SCHOOLS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ObjectType::CANTONS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ObjectType::COUNTRIES)));
    }

    public function createUniversityQualificationSelects()
    {

        $sertificateTypes = $this->enrolmentConfigurationReferenceObjectsRepository->getEntities(ObjectType::CERTIFICATE_TYPE);

        $this->selects[0] = [];
        $certTypeSelect = [];
        $index = 0;
        foreach ($sertificateTypes as $certificateId => $certificateProperties) {
            $newSelectKey = count($this->selects);
            $this->selects[$newSelectKey] = $this->getIssueYearSelectForCertificateType(EntityKey::new($certificateId, ObjectType::CERTIFICATE_TYPE));
            $certTypeSelect[] = [
                $index,
                $newSelectKey
            ];
        }
        $this->selects[0] = [InputType::CERTIFICATE_TYPE->value, $certTypeSelect];

        $this->writeReferenceObject(ObjectType::UNIVERSITY_QUALIFICATION_SELECTS, $this->selects);
        $index = $index + 1;
    }

    public function writeReferenceObject(ObjectType $referenceObjectName, array|object $referenceObject): void
    {
        $filePath = $this->configFilesDirectoryPath . "/reference-objects/" . $referenceObjectName->value . ".json";
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $jsonDocument = fopen($filePath, "w");
        fwrite($jsonDocument, json_encode($referenceObject));
    }

    private function getIssueYearSelectForCertificateType(EntityKey $certificateTypeKey)
    {
        $issueYears = $this->enrolmentConfigurationReferenceObjectsRepository->getIssueYearsForCertificateType($certificateTypeKey);
        foreach ($issueYears as $issueYearIndex => $issueYear) {
            $newSelectKey = count($this->selects);
            $this->selects[$newSelectKey] = $this->getCertificateSelectForCertificateTypeAndIssueYear($certificateTypeKey, $issueYear);
            $issueYearSelect[] = [
                $issueYearIndex,
                $newSelectKey
            ];
        }
        return [InputType::ISSUE_YEAR->value, $issueYearSelect];
    }

    private function getCertificateSelectForCertificateTypeAndIssueYear(EntityKey $certificateTypeKey, int $issueYear)
    {

        $certificateTypes = $this->enrolmentConfigurationReferenceObjectsRepository->getCertificatesForCertificateTypeAndIssueYear($certificateTypeKey, $issueYear);
        $select = [];
        foreach ($certificateTypes as $index => $certificateType) {
            //$newSelectKey = count($this->selects);
            // $this->selects[$newSelectKey] = [];
            $select[] = [
                $index,
                //$newSelectKey
            ];
        }
        return [InputType::CERTIFICATE->value, $select];

    }

}