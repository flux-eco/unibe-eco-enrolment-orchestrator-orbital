<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\InputsAdapter;

use JsonSerializable;

final readonly class OptionList implements JsonSerializable
{

    private function __construct(
        public int   $index,
        public array $items,
    )
    {

    }

    /**
     * @param int $index
     * @param JsonSerializable[] $items
     * @return self
     */
    public static function new(
        int   $index,
        array $items,
    )
    {
        return new self($index, $items);
    }


    /**
     * @param int $index
     * @param array $items
     * @param callable $transformOptionItemCallable
     * @return self
     */
    public static function newWithOptionItemTransformation(int $index, array $items, callable $transformOptionItemCallable): self
    {
        $tranformedItems = [];
        foreach ($items as $item) {
            $tranformedItems[] = $transformOptionItemCallable($item);
        }
        return self::new($index, $tranformedItems);
    }


    public function jsonSerialize(): array
    {
        return [$this->index => $this->items];
    }
}