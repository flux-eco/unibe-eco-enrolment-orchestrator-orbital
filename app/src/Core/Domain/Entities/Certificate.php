<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class Certificate
{
    /**
     * @param string $id
     * @param  ValueObjects\Label $label
     * @param int $minIssueYear
     * @param int $maxIssueYear
     * @param int $certificateTypeId
     */
    private function __construct(
        public string             $id,
        public  ValueObjects\Label              $label,
        public int                $minIssueYear,
        public int                $maxIssueYear,
        public int                $certificateTypeId
    )
    {

    }

    /**
     * @param string $id
     * @param  ValueObjects\Label $label
     * @param int $minIssueYear
     * @param int $maxIssueYear
     * @param int $certificateTypeId
     * @return static
     */
    public static function new(
        string             $id,
        ValueObjects\Label              $label,
        int                $minIssueYear,
        int                $maxIssueYear,
        int                $certificateTypeId
    ): self
    {
        if ($minIssueYear < date('Y') - 60) {
            $minIssueYear = date('Y') - 60;
        }
        if ($maxIssueYear > date('Y')) {
            $maxIssueYear = date('Y');
        }

        return new self(...get_defined_vars());
    }
}