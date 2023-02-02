<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ReferenceObjects\ReferenceObjectName;

final class EnrolmentConfigurationAggregate
{
    private function __construct(
        public MessageRecorder $messageRecorder
    )
    {

    }

    public static function new(
        MessageRecorder $messageRecorder
    ): self
    {
        return new self($messageRecorder);
    }

    public function createOrUpdateSpecification(): void
    {
        $header = OutgoingMessages\MessageHeaders::new(
            OutgoingMessages\MessageName::CREATE_REFERENCE_OBJECT->value,
            OutgoingMessages\MessageName::CREATE_REFERENCE_OBJECT->toAddress([]),
        );
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::SUBJECTS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::DEGREE_PROGRAMS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::SEMESTERS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::CERTIFICATE_TYPES)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::GRADUATION_TYPES)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::CERTIFICATES)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::PLACES)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::SCHOOLS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::CANTONS)));
        $this->messageRecorder->record(OutgoingMessages\Message::new($header, OutgoingMessages\CreateReferenceObject::new(ReferenceObjectName::COUNTRIES)));
    }

}