<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class Certificate
{
    /**
     * @param string $id
     * @param ValueObjects\LocalizedStringValue[] $label
     * @param int $minIssueYear
     * @param int $maxIssueYear
     * @param int $certificateTypeId
     */
    private function __construct(
        public string             $id,
        public array              $label,
        public int                $minIssueYear,
        public int                $maxIssueYear,
        public int                $certificateTypeId
    )
    {

    }

    /**
     * @param string $id
     * @param ValueObjects\LocalizedStringValue[] $label
     * @param int $minIssueYear
     * @param int $maxIssueYear
     * @param int $certificateTypeId
     * @return static
     */
    public static function new(
        string             $id,
        array              $label,
        int                $minIssueYear,
        int                $maxIssueYear,
        int                $certificateTypeId
    ): self
    {
        return new self(...get_defined_vars());
    }
}