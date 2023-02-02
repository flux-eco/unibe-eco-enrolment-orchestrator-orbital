<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports\Dispatchers;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages\Message;

interface ConfigurationMessageDispatcher
{
    public function dispatch(Message $message): void;
}