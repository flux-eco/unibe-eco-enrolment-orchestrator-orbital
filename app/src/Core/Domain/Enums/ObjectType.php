<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums;
enum ObjectType: string
{
    case BASE_DATA = "base-data";
    case DEGREE_PROGRAMS = "degree-programs";
    case SUBJECTS = "subjects";
    case SUBJECT_COMBINATIONS = "subject-combinations";
    case COUNTRIES = "countries";
    case SEMESTERS = "semesters";
    case MIN_PASSWORD_LENGTH = "minPasswordLength";
    case LANGUAGE = "language";
    case SALUTATIONS = 'salutations';

    case AREA_CODES = "area-codes";
    case CANTONS = "cantons";
    case CERTIFICATE_TYPES = "certificate-types";

    case CERTIFICATE_TYPE = "certificate-type";
    case GRADUATION_TYPES = "graduation-types";
    case CERTIFICATES = "certificates";

    case CERTIFICATE = "certificate";
    case CERTIFICATES_ISSUE_YEARS = "certificates-issue-years";
    case UNIVERSITY_QUALIFICATION_SELECTS = "university-qualification-selects";
    case LANGUAGES = "languages";
    case PHOTO_TYPE = "photo-type";
    case PLACES = "places";
    case SCHOOLS = "schools";
    case CHOICE_SUBJECT = "choice-subject";
    case COMPLETED = "completed";
    case IDENTIFICATION_NUMBER = "identification-number";
    case INTENDED_DEGREE_PROGRAM = "intended-degree-program";
    case INTENDED_DEGREE_PROGRAM_2 = "intended-degree-program-2";
    case LEGAL = "legal";
    case PORTRAIT = "portrait";
    case UNIVERSITY_ENTRANCE_QUALIFICATION = "university-entrance-qualification";
    case CERTIFICATE_TYPE_YEAR_RANGE = "certificateTypeYearRange";

    case LABEL = "label";



    public function toLabel(): string
    {
        return $this->value;
    }

    public function toParameterName(): string
    {
        return $this->value;
    }
}