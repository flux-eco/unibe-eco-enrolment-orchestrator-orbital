<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages;
final readonly class MessageHeaders
{
    private function __construct(
        public string $name,
        public string $address,
        public array $cookies
    ) {

    }

    public static function new(
        string $name,
        string $address,
        array $cookies = []
    ) {
        return new self(...get_defined_vars());
    }
}