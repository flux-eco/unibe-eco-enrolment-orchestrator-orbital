<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class KeyValue
{
    private function __construct(
        public int|string $key,
        public mixed      $value
    )
    {

    }

    public static function new(int|string $key, mixed $value): self
    {
        return new self($key, $value);
    }

    public function equals(KeyValue $other)
    {
        return $this->key === $other->key && $this->value === $other->value;
    }
}