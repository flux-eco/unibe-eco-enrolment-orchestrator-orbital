<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
enum ConfigurationObjectName: string
{
    case LAYOUT = "layout";

    public function toLabel(): string
    {
        return $this->value;
    }

    public function toParameterName(): string
    {
        return match ($this) {
            default => $this->value
        };
    }

    public function toObject(string $configObjectDirectoryPath): object
    {
        return json_decode(file_get_contents($this->toJsonFilePath($configObjectDirectoryPath)));
    }

    public function toJsonFilePath(string $valueObjectConfigDirectoryPath): string
    {
        return $valueObjectConfigDirectoryPath . "/" . $this->value . ".json";
    }
}