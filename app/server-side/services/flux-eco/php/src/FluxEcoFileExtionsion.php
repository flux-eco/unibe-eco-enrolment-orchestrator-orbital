<?php

namespace FluxEcoType;

enum FluxEcoFileExtionsion: string
{
    case JSON = "json";

    public function toContentType(): string
    {
        return match ($this) {
            self::JSON => FluxEcoContentType::APPLICATION_JSON->value
        };
    }
}