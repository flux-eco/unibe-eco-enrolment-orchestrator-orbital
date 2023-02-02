<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ReferenceObjects;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class DegreeProgramme implements \JsonSerializable
{
    /**
     * @param string $id
     * @param ValueObjects\Label $label
     * @param int $ect
     * @param DegreeProgrammeType[] $combinations
     * @param bool $consecutive
     */
    private function __construct(
        public string             $id,
        public ValueObjects\Label $label,
        public int                $ect,
        public array              $combinations,
        public bool               $consecutive
    )
    {

    }

    /**
     * @param string $id
     * @param ValueObjects\Label $label
     * @param int $ect
     * @param DegreeProgrammeType[] $combinations
     * @param bool $consecutive
     * @return DegreeProgramme
     */
    public static function new(
        string             $id,
        ValueObjects\Label $label,
        int                $ect,
        array              $combinations,
        bool               $consecutive
    )
    {
        return new self(...get_defined_vars());
    }

    public function jsonSerialize(): array
    {
        $combinations = [];
        foreach ($this->combinations as $combination) {
            $combinations[] = $combination->jsonSerialize();
        }

        return [
            'id' => $this->id,
            'label' => $this->label,
            'ect' => $this->ect,
            'combinations' => $combinations,
            'consecutive' => $this->consecutive
        ];
    }
}