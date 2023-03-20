<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
final class UniqueValueCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var KeyValue[]
     */
    private array $items;

    private function __construct(array $items = [])
    {
        $this->items = [];

        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param KeyValue[] $items
     * @return UniqueValueCollection
     */
    public static function fromArray(array $items): UniqueValueCollection
    {
        $uniqueItems = [];
        $values = [];

        foreach ($items as $item) {
            $value = $item->value;
            if (!in_array($value, $values, true)) {
                $values[] = $value;
                $uniqueItems[] = $item;
            }
        }

        return new self($uniqueItems);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function add(KeyValue $item): void
    {
        if (!$this->contains($item)) {
            $this->items[] = $item;
        }
    }

    public function remove(KeyValue $item)
    {
        $key = array_search($item, $this->items, true);

        if ($key !== false) {
            unset($this->items[$key]);
        }
    }

    public function contains(KeyValue $item): bool
    {
        $value = $item->value;
        foreach ($this->items as $existingItem) {
            if ($existingItem->value === $value) {
                return true;
            }
        }

        return false;
    }

    public function get(int|string $key): mixed
    {
        foreach ($this->items as $existingItem) {
            if ($existingItem->key === $key) {
                return $existingItem->value;
            }
        }
        return null;
    }

    /**
     * @return KeyValue[]
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
