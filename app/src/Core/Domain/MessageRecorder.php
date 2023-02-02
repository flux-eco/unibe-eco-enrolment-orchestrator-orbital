<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain;

final class MessageRecorder
{
    /**
     * @param OutgoingMessages\Message[] $recordedMessages
     */
    private function __construct(
        public array $recordedMessages = []
    ) {

    }

    public static function new() : self
    {
        return new self();
    }

    public function record(OutgoingMessages\Message $message)
    {
        $this->recordedMessages[] = $message;
    }

    public function flush() : void
    {
        $this->recordedMessages = [];
    }
}