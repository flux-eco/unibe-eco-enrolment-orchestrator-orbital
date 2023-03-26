<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use InvalidArgumentException;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;

final class CertificateTypeIssueYearRange
{
    public ObjectType $objectType;

    private static $instances = [];

    private function __construct(public int $certificateTypeId, public int $minYear, public int $maxYear)
    {
        if ($minYear < 0 || $maxYear < 0 || $minYear > $maxYear) {
            throw new InvalidArgumentException('Invalid year range');
        }
        $this->objectType = ObjectType::CERTIFICATE_TYPE_YEAR_RANGE;
    }

    public static function new(int $certificateTypeId, int $minYear, int $maxYear): self
    {
        if ($minYear < 0 || $maxYear < 0 || $minYear > $maxYear) {
            throw new InvalidArgumentException('Invalid year range');
        }
        if ($minYear < date('Y') - 60) {
            $minYear = date('Y') - 60;
        }
        if ($maxYear > date('Y')) {
            $maxYear = date('Y');
        }

        // $key = "{$certificateTypeId}-{$minYear}-{$maxYear}";

        if (!isset(self::$instances[$certificateTypeId])) {
            self::$instances[$certificateTypeId] = new self($certificateTypeId, $minYear, $maxYear);
        }

        $instance = self::$instances[$certificateTypeId];
        if ($instance->minYear >= $minYear) {
            $instance->minYear = $minYear;
        }
        if ($instance->maxYear <= $maxYear) {
            $instance->maxYear = $maxYear;
        }
        self::$instances[$certificateTypeId] = $instance;

        return self::$instances[$certificateTypeId];
    }

    public function contains(int $year): bool
    {
        return ($year > $this->minYear && $year < $this->maxYear);
    }

    public function __toString(): string
    {
        return $this->minYear . '-' . $this->maxYear;
    }
}