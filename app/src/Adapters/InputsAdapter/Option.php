<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\InputsAdapter;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\DataAdapter;
use JsonSerializable;

final readonly class Option implements JsonSerializable
{

    private function __construct(
        public string            $id,
        public null|DataAdapter\Label|string $label = null
    )
    {

    }

    public static function fromDataObject(
        object  $data,
        string  $keyNameId,
        ?string $keyNameLabel = null
    ): self
    {
        if ($keyNameLabel === null) {
            return new self($data->{$keyNameId});
        }
        return new self($data->{$keyNameId}, $data->{$keyNameLabel});
    }


    public function jsonSerialize(): array
    {
        if (is_null($this->label)) {
            return [$this->id => ['id' => $this->id]];
        }
        return [$this->id => ['id' => $this->id, 'label' => $this->label]];
    }
}