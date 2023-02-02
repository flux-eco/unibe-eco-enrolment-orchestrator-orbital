<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
enum ValueObjectName: string
{
    case LANGUAGECODE = "languagecode";
    case LAYOUT = "layout";
    case IDENTIFICATION_NUMBER = "identification-number";
    case SESSION_ID = "session-id";
    case CURRENT_PAGE = "current-page";
    case ENROLMENT_DATA = "enrolment-data";

    public function toLabel(): string
    {
        return $this->value;
    }

    public function toParameterName(): string
    {
        return match ($this) {
            self::LANGUAGECODE => 'pLanguagecode',
            default => $this->value
        };
    }

    public function toParameterString(string|int $value): string
    {
        return $this->value . "/" . $value;
    }

    public function toJsonFilePath(string $valueObjectConfigDirectoryPath): string
    {
        return $valueObjectConfigDirectoryPath . "/" . $this->value . ".json";
    }
}