<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

use InvalidArgumentException;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\CaseFormat;

final readonly class CaseFormattedStringValue: string
{
    private function __construct(public string $value, public CaseFormat $caseFormat)
    {

    }

    public static function fromString(string $value): self
    {
        $caseFormat = null;
        if (preg_match('/^\p{Lu}/u', $value)) {
            $caseFormat = CaseFormat::PASCAL_CASE;
        } elseif (strpos($value, '_') !== false) {
            $caseFormat = CaseFormat::CAMEL_CASE;
        } elseif (strpos($value, '-') !== false) {
            $caseFormat = CaseFormat::KEBAB_CASE;
        }

        if ($caseFormat === null) {
            throw new InvalidArgumentException("Invalid string format: $value");
        }

        return new self($value, $caseFormat);
    }

    /**
     * @throws \Exception
     */
    public function toSnakeCase(): self
    {
        return new self($this->caseFormat->toSnakeCase($this->value), CaseFormat::SNAKE_CASE);
    }

    /**
     * @throws \Exception
     */
    public function toCamelCase(): self
    {
        return new self($this->caseFormat->toCamelCase($this->value), CaseFormat::CAMEL_CASE);
    }

    /**
     * @throws \Exception
     */
    public function toPascalCase(): self
    {
        return new self($this->caseFormat->toPascalCase($this->value), CaseFormat::PASCAL_CASE);
    }
}