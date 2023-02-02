<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\Dispatchers;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages\Message;

interface EnrolmentMessageDispatcher
{
    public function dispatch(Message $message, callable $publish): void;
}