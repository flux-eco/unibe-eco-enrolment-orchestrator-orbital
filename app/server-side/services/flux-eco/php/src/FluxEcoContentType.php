<?php

namespace FluxEcoType;

enum FluxEcoContentType: string{
    case APPLICATION_JSON =  "application/json";

    public function asHttpHeader(): array {
        return ["Content-Type", $this->value];
    }
}