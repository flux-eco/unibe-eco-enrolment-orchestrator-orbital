<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;

final readonly class DependentInputSelect implements \JsonSerializable
{
    /**
     * @param int $selectIndex
     * @param DependentSelectInputOption[] $dependentInputOptions
     */
    private function __construct(
        public int       $selectIndex,
        public int       $selectToDataType,
        public array $dependentInputOptions
    )
    {

    }

    /**
     * @param int $selectIndex
     * @param DependentSelectInputOption[] $dependentInputChoices
     * @return static
     */
    public static function new(
        int       $selectIndex,
        int       $selectToDataType,
        array     $dependentInputChoices,
    ): self
    {
        return new self(
            $selectIndex,
            $selectToDataType,
            $dependentInputChoices,
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            $this->selectToDataType,
            $this->dependentInputOptions
        ];
    }
}