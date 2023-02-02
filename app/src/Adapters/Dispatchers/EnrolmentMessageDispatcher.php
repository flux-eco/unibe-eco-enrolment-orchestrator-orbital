<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Dispatchers;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\ConfigurationObjectName;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\Dispatchers;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages\MessageName;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages\Message;

final readonly class EnrolmentMessageDispatcher implements Dispatchers\EnrolmentMessageDispatcher
{
    private function __construct(

    )
    {

    }

    public static function new(): self
    {
        return new self(...get_defined_vars());
    }

    public function dispatch(Message $message, callable $publish): void
    {
        match (MessageName::from($message->headers->name)) {
            default => $publish($message)
        };
    }
}