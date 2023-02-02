<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;

use JsonSerializable;

final readonly class Message
{
    private function __construct(
        public MessageHeaders $headers,
        public object $payload
    )
    {

    }

    public static function new(
        MessageHeaders $headers,
        object $payload
    ) : self
    {
        return new self(
            ...get_defined_vars()
        );
    }
}