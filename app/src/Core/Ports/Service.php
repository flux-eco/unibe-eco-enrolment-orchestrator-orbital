<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\IncomingMessages;

final readonly class Service
{
    private function __construct(
        private Outbounds $outbounds
    )
    {

    }

    public static function new($outbounds): self
    {
        return new self($outbounds);
    }

    public function createEnrolmentConfiguration(
        IncomingMessages\CreateEnrolmentConfiguration $createEnrolmentConfiguration
    ): void
    {
        $messageRecorder = Domain\MessageRecorder::new();
        $aggregate = Domain\EnrolmentConfigurationAggregate::new(
            $messageRecorder
        );
        $aggregate->createOrUpdateSpecification();

        foreach ($aggregate->messageRecorder->recordedMessages as $message) {
            $this->outbounds->configurationMessageDispatcher->dispatch(
                $message
            );
        }

        $aggregate->messageRecorder->flush();
    }

    public function provideLayout(IncomingMessages\ProvideLayout $message, callable $publish): void
    {
        $messageRecorder = Domain\MessageRecorder::new();
        $aggregate = Domain\EnrolmentAggregate::new(
            $messageRecorder,
            $this->outbounds->enrolmentRepository
        );
        $aggregate->provideLayout($message->valueObjectsConfigDirectoryPath);

        foreach ($messageRecorder->recordedMessages as $message) {
            $this->outbounds->enrolmentMessageDispatcher->dispatch($message, $publish);
        }
    }

    public function providePage(IncomingMessages\ProvidePage $message, callable $publish): void
    {
        $messageRecorder = Domain\MessageRecorder::new();
        $aggregate = Domain\EnrolmentAggregate::new(
            $messageRecorder,
            $this->outbounds->enrolmentRepository
        );
        $aggregate->providePage($message->pageObjectDirectoryPath, $message->currentPage, $message->enrolmentData);
        foreach ($messageRecorder->recordedMessages as $message) {
            $this->outbounds->enrolmentMessageDispatcher->dispatch($message, $publish);
        }
    }

    public function storeData(IncomingMessages\StoreData $message, callable $publish)
    {
        $messageRecorder = Domain\MessageRecorder::new();
        $aggregate = Domain\EnrolmentAggregate::new(
            $messageRecorder,
            $this->outbounds->enrolmentRepository
        );
        $aggregate->storeData($message->pageName, $message->sessionId, $message->dataToStore, $message->enrolmentData);

        foreach ($messageRecorder->recordedMessages as $message) {
            $this->outbounds->enrolmentMessageDispatcher->dispatch($message, $publish);
        }
    }
}