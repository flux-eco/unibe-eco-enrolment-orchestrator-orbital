<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
final class KeyValueCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array mixed[] $items
     */
    private array $items;

    /**
     * @param KeyValue[] $items
     */
    private function __construct(array $keyValueItems = [])
    {
        $this->items = [];

        foreach ($keyValueItems as $keyValueItem) {
            $this->add($keyValueItem);
        }
    }

    /**
     * @return KeyValueCollection
     */
    public static function fromArray(array $keyValueItems): KeyValueCollection
    {
        return new self($keyValueItems);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function add(KeyValue $keyValueItem): void
    {
        $this->items[$keyValueItem->key] = $keyValueItem->value;
    }

    public function get(int|string $key): mixed
    {
        if ($this->contains($key) === false) {
            return null;
        }
        return $this->items[$key];
    }

    public function remove(int|string $key): void
    {
        if ($this->contains($key) === true) {
            unset($this->items[$key]);
        }
    }

    public function contains(int|string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function toArray(): array
    {
        return array_values($this->items);
    }
}
