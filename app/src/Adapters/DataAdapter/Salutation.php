<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\DataAdapter;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\Portrait;

final readonly class Salutation implements Portrait\Types\Salutation
{
    private function __construct(
        public string $id,
        public Label  $label,
    )
    {

    }

    public static function new(
        string $id,
        Label  $label,
    ): self
    {
        return new self($id, $label);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): Label
    {
        return $this->label;
    }
}