<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class Combination implements \JsonSerializable
{
    private function __construct(
        public string $id,
        public Label $label,
        public int $ect,
        public array $mandatory,
        public array $singleChoice,
        public array $multipleChoice
    ) {

    }

    public static function new(
        string $id,
        Label $label,
        int $ect,
        array $mandatory,
        array $singleChoice,
        array $multipleChoice
    ): self {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "label" => $this->label,
            "ect" => $this->ect,
            "mandatory" => $this->mandatory,
            "single-choice" => $this->singleChoice,
            "multiple-choice" => $this->multipleChoice,
        ];
    }
}