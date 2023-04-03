<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\DataAdapter;
use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification;

final readonly class Certificate implements UniversityEntranceQualification\Types\Certificate
{
    /**
     * @param string $id
     * @param Label $label
     * @param int $minIssueYear
     * @param int $maxIssueYear
     * @param int $certificateTypeId
     */
    private function __construct(
        public string             $id,
        public  Label              $label,
        public int                $minIssueYear,
        public int                $maxIssueYear,
        public int                $certificateTypeId
    )
    {

    }

    /**
     * @param string $id
     * @param  Label $label
     * @param int $minIssueYear
     * @param int $maxIssueYear
     * @param int $certificateTypeId
     * @return static
     */
    public static function new(
        string             $id,
        Label              $label,
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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Label
     */
    public function getLabel(): Label
    {
        return $this->label;
    }

    /**
     * @return int
     */
    public function getMinIssueYear(): int
    {
        return $this->minIssueYear;
    }

    /**
     * @return int
     */
    public function getMaxIssueYear(): int
    {
        return $this->maxIssueYear;
    }

    /**
     * @return int
     */
    public function getCertificateTypeId(): int
    {
        return $this->certificateTypeId;
    }


}