<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;


final readonly class DependentSelectInputOption implements \JsonSerializable
{
    private function __construct(
        public string $choiceIndex,
        public int $nextSelectIndex,
    )
    {

    }

    public static function new(
        string $choiceIndex,
        int $nextSelectIndex,
    ): self
    {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize(): array
    {
        return [
            $this->choiceIndex,
            $this->nextSelectIndex
        ];
    }
}