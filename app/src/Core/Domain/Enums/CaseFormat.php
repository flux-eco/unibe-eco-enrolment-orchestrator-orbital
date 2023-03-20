<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums;

use InvalidArgumentException;

enum CaseFormat: string
{
    case SNAKE_CASE = "snake_case";
    case KEBAB_CASE = "kebab-case";
    case PASCAL_CASE = "PascalCase";
    case CAMEL_CASE = "camelCase";


    /**
     * @throws \Exception
     */
    public function toSnakeCase(string $input): string
    {
        return match ($this) {
            self::KEBAB_CASE, self::PASCAL_CASE, self::CAMEL_CASE => str_replace('-', '_', $this->toKebabCase($input)),
            self::SNAKE_CASE => $input,
        };
    }

    public function toKebabCase(string $input): string
    {
        return match ($this) {
            self::KEBAB_CASE => $input,
            self::SNAKE_CASE => str_replace('_', '-', $input),
            self::PASCAL_CASE => throw new \Exception('To be implemented'),
            self::CAMEL_CASE => throw new \Exception('To be implemented')
        };
    }

    /**
     * @throws \Exception
     */
    public function toCamelCase(string $input): string
    {
        return match ($this) {
            self::KEBAB_CASE, self::PASCAL_CASE, self::SNAKE_CASE => lcfirst($this->toPascalCase($input)),
            self::CAMEL_CASE => $input
        };
    }

    public function toPascalCase(string $input): string
    {
        return match ($this) {
            self::KEBAB_CASE => str_replace(' ', '', ucwords(str_replace('-', ' ', $input))),
            self::SNAKE_CASE => throw new \Exception('To be implemented'),
            self::PASCAL_CASE => $input,
            self::CAMEL_CASE => ucfirst($input)
        };
    }
}
