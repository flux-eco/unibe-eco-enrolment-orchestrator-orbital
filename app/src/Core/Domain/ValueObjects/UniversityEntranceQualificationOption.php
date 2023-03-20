<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class UniversityEntranceQualificationOption
{
    private function __construct(
        public InputType $type,
        public int       $parentValue,
        public int       $selectIndex
    )
    {

    }

    public static function new(
        InputType $type,
        int       $parentValue,
        int       $selectIndex
    )
    {
        return new self(...get_defined_vars());
    }
}