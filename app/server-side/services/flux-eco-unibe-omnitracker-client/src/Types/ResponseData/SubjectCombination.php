<?php

namespace FluxEco\UnibeOmnitrackerClient\Types\ResponseData;

final readonly class SubjectCombination
{
    private function __construct(
        public string $id,
        public Label  $label,
        public array  $mandatory,
        public array  $singleChoice,
        public array  $multipleChoice
    )
    {

    }

    public static function new(
        string $id,
        Label  $label,
        array  $mandatory,
        array  $singleChoice,
        array  $multipleChoice
    )
    {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'mandatory' => $this->mandatory,
            'single-choice' => $this->singleChoice,
            'multiple-choice' => $this->multipleChoice
        ];
    }
}