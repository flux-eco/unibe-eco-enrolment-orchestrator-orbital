<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ReferenceObjects;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
final readonly class Page
{

    private function __construct(
        public string $page,
        public object $data,
        public bool $canBack
    ) {
    }

    /**
     * @param string   $name
     * @param object $pageData
     * @return void
     */
    static function new(
        string $name,
        object $data,
        bool $canBack
    ) : self {

        return new self(
            $name,
            $data,
            $canBack
        );
    }

    public function jsonSerialize() : array
    {
        return (array) $this;
    }
}