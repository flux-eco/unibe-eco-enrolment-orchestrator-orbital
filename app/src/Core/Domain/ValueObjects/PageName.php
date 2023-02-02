<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
enum PageName: string
{
    case START = "start";
    case CREATE = "create";
    case IDENTIFICATION_NUMBER = "identification-number";
    case CHOICE_SUBJECT = "choice-subject";
    case INTENDED_DEGREE_PROGRAM = "intended-degree-program";
    case INTENDED_DEGREE_PROGRAM_2 = "intended-degree-program-2";
    case UNIVERSITY_ENTRANCE_QUALIFICATION = "university-entrance-qualification";
    case PORTRAIT = "portrait";
    case PAGE_DATA = "personal-data";
    case LEGAL = "legal";
    case COMPLETED = "completed";

    public static function fromLastPage(string $lastPage): self {
        return match($lastPage) {
            self::START->value => self::IDENTIFICATION_NUMBER,
            self::IDENTIFICATION_NUMBER->value => self::CHOICE_SUBJECT,
            self::CHOICE_SUBJECT->value => self::INTENDED_DEGREE_PROGRAM,
            self::INTENDED_DEGREE_PROGRAM->value => self::INTENDED_DEGREE_PROGRAM_2,
            self::INTENDED_DEGREE_PROGRAM_2->value => self::UNIVERSITY_ENTRANCE_QUALIFICATION,
            self::UNIVERSITY_ENTRANCE_QUALIFICATION->value => self::PORTRAIT,
            self::PORTRAIT->value => self::PAGE_DATA,
            self::PAGE_DATA->value => self::LEGAL,
            self::LEGAL->value => self::COMPLETED,
            default => self::START
        };
    }

    public function toObject(string $pageObjectDirectoryPath): object
    {
        return json_decode(file_get_contents($this->toJsonFilePath($pageObjectDirectoryPath)));
    }

    public function toJsonFilePath(string $pageObjectDirectoryPath): string
    {
        return $pageObjectDirectoryPath . "/" . $this->value . ".json";
    }
}