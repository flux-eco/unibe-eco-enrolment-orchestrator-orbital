<?php
namespace FluxEco\UnibeOmnitrackerClient\Types\ResponseData;

use JsonSerializable;

final readonly class Label implements JsonSerializable
{
    private function __construct(
        public ?string $de,
        public ?string $en = null
    )
    {

    }

    public static function newGermanLabel(string $de): self
    {
        return new self($de);
    }

    public static function newEnglishLabel(string $en): self
    {
        return new self(null, $en);
    }

    public static function new(
        string $de,
        string $en
    ): self
    {
        return new self($de, $en);
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}