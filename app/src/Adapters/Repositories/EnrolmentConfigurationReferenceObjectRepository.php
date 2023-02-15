<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Repositories;

use stdClass;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\DegreeProgramConfig;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\SoapFile;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\SoapParameters;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages\{
    Message,
    MessageName,
    CreateReferenceObject
};
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ReferenceObjects\{ReferenceObjectName,
    LabelValueReferenceObject,
    DegreeProgramme,
    DegreeProgrammeType,
    Subject,
    SubjectChoice,
    SubjectCombination,
    Choice
};
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\{LanguageCode,
    Server,
    Credentials,
    Label,
    MandatoryType,
    ChoiceType,
    ValueObjectName
};
use JsonSerializable;
use SoapFault;

class EnrolmentConfigurationReferenceObjectRepository
{
    /**
     * @param Server $soapWsdlServer
     * @param string $configFilesDirectoryPath
     * @param string $soapServerHost
     * @param Credentials $soapCredentials
     * @param DegreeProgramConfig[] $degreeProgramSubjectFilter
     */
    private function __construct(
        private Server      $soapWsdlServer,
        private string      $configFilesDirectoryPath,
        private string      $soapServerHost,
        private Credentials $soapCredentials,
        private array       $degreeProgramSubjectFilter
    )
    {

    }

    public static function new(string $configFilesDirectoryPath, Server $soapWsdlServer, string $soapServerHost, Credentials $soapCredentials, array $degreeProgramSubjectFilter): self
    {
        return new self(...get_defined_vars());
    }

    public function get(ReferenceObjectName $referenceObjectName)
    {

    }

    /**
     * @param CreateReferenceObject $payload
     * @return void
     * @throws \Exception
     */
    public function createReferenceObject(object $payload): void
    {
        match ($payload->referenceObjectName) {
            ReferenceObjectName::SUBJECTS => $this->createSubjects(),
            ReferenceObjectName::DEGREE_PROGRAMS => $this->createDegreeProgrammes(),
            ReferenceObjectName::COUNTRIES => $this->createCountries(),
            ReferenceObjectName::SEMESTERS => $this->createSemesters(),
            ReferenceObjectName::MIN_PASSWORD_LENGTH => [],
            ReferenceObjectName::LANGUAGE => [],
            ReferenceObjectName::SALUTATIONS => [],
            ReferenceObjectName::AREA_CODES => [],
            ReferenceObjectName::CANTONS => $this->createCantons(),
            ReferenceObjectName::CERTIFICATE_TYPES => $this->createCertificateTypes(),
            ReferenceObjectName::GRADUATION_TYPES => $this->createGraduationTypes(),
            ReferenceObjectName::CERTIFICATES => $this->createCertificates(),
            ReferenceObjectName::PHOTO_TYPE => [],
            ReferenceObjectName::PLACES => $this->createPlaces(),
            ReferenceObjectName::SCHOOLS => [],
            ReferenceObjectName::CHOICE_SUBJECT => [],
            ReferenceObjectName::COMPLETED => [],
            ReferenceObjectName::IDENTIFICATION_NUMBER => [],
            ReferenceObjectName::INTENDED_DEGREE_PROGRAM => [],
            ReferenceObjectName::INTENDED_DEGREE_PROGRAM_2 => [],
            ReferenceObjectName::LEGAL => [],
            ReferenceObjectName::PORTRAIT => [],
            ReferenceObjectName::UNIVERSITY_ENTRANCE_QUALIFICATION => [],
            ReferenceObjectName::BASE_DATA => throw new \Exception('To be implemented'),
            ReferenceObjectName::SUBJECT_COMBINATIONS => throw new \Exception('To be implemented'),
            ReferenceObjectName::LANGUAGES => throw new \Exception('To be implemented'),
        };
    }

    private function createCountries(): void
    {
        $list = $this->queryHelpTable('GetListStaat', ['GetListStaatResult']);
        $this->writeReferenceObject(ReferenceObjectName::COUNTRIES, $list);
    }

    private function createCantons(): void
    {
        $list = $this->queryCantons('GetListKanton', ['GetListKantonResult']);
        $this->writeReferenceObject(ReferenceObjectName::CANTONS, $list);
    }

    private function createSchools(): void
    {
        $list = $this->queryCantonsWithSchoolsByCertificate('GetListSchuleMaturitaet', ['GetListSchuleMaturitaetResult']);
        $this->writeReferenceObject(ReferenceObjectName::SCHOOLS, $list);
    }

    private function createPlaces(): void
    {
        $list = $this->queryPlaces('GetListGemeinde', ['GetListGemeindeResult']);
        $this->writeReferenceObject(ReferenceObjectName::PLACES, $list);
    }

    private function createCertificateTypes(): void
    {
        $list = $this->queryCertificateTypes('GetListStudienberechtigungsausweistyp', ['GetListStudienberechtigungsausweistypResult'], ['pType' => 1]);
        $this->writeReferenceObject(ReferenceObjectName::CERTIFICATE_TYPES, $list);
    }

    private function createGraduationTypes(): void
    {
        $list = $this->queryHelpTable('GetListAbschlusstyp', ['GetListAbschlusstypResult', 'ObjAbschlusstyp']);
        $this->writeReferenceObject(ReferenceObjectName::GRADUATION_TYPES, $list);
    }

    private function createCertificates(): void
    {
        $list = $this->queryCertificates('GetListStudienberechtigungsausweis', ['GetListStudienberechtigungsausweisResult']);
        $this->writeReferenceObject(ReferenceObjectName::CERTIFICATES, $list);
    }

    private function createSubjects(): void
    {
        $subjects = $this->querySubjects();
        $this->writeReferenceObject(ReferenceObjectName::SUBJECTS, $subjects);

        $this->writeReferenceObject(ReferenceObjectName::SUBJECT_COMBINATIONS, $this->extractSubjectCombinations($subjects));
    }

    private function createDegreeProgrammes(): void
    {
        $qualifications = $this->queryQualifications();
        $degreeProgramms = $this->queryHelpTable('GetListStudienstufe', ['GetListStudienstufeResult']);

        $dataList = [];
        foreach ($degreeProgramms as $key => $value) {
            $obj = new stdClass();
            $obj = $value;
            $obj->qualifications = $qualifications[$value->{'id'}];

            $dataList[$key] = $obj;
        }
        $this->writeReferenceObject(ReferenceObjectName::DEGREE_PROGRAMS, $dataList);
    }

    private function createSemesters()
    {
        $semesters = $this->queryHelpTable('GetListSemester', ['GetListSemesterResult']);
        $this->writeReferenceObject(ReferenceObjectName::SEMESTERS, $semesters);
    }

    private function queryCantonsWithSchoolsByCertificate(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $cantons = $this->queryCantons('GetListKanton', ['GetListKantonResult']);
        $countries = $this->queryCountriesCantonsPlaces('GetListStaat', ['GetListStaatResult']);

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::GERMAN, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::ENGLISH, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }

        $dataList = [];

        $i = 50;
        foreach ($germanDatalist as $germanData) {
            $i = $i+1;
            if($i>= 50) {
                break;
            }
            $obj = new stdClass();
            $obj->id = $germanData->UniqueId;
            $obj->label = Label::new(
                $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                $germanData->Title
            );
            $obj->schoolUniqueId = $germanData->SchuleUniqueId;
            $obj->typeId = $germanData->TypUniqueId;
            $obj->certificateId = $germanData->StudienberechtigungsausweisUniqueId;
            $obj->countries = $countries; //todo work with references $ref  because of memory


            if (array_key_exists($obj->certificateId, $dataList) === false || array_key_exists($germanData->CantonUniqueId, $dataList[$obj->certificateId]) === false) {
                if (array_key_exists($germanData->CantonUniqueId, $cantons)) {
                    $dataList[$obj->certificateId][$germanData->CantonUniqueId] = $cantons[$germanData->CantonUniqueId];
                }
            }

            if (array_key_exists($obj->certificateId, $dataList) === true && array_key_exists($germanData->CantonUniqueId, $dataList[$obj->certificateId]) === true) {
                $dataList[$obj->certificateId][$germanData->CantonUniqueId]->schools[] = $obj;
            }

        }

        return $dataList;
    }

    private function queryPlaces(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::GERMAN, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::ENGLISH, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {
            $obj = new stdClass();
            $obj->id = $germanData->UniqueId;
            $obj->label = Label::new(
                $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                $germanData->Title
            );
            $obj->cantonId = $germanData->KantonUniqueId;
            $dataList[$obj->cantonId][] = $obj;
        }
        return $dataList;
    }

    private function queryCantons(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::GERMAN, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::ENGLISH, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {
            $dataList[] = LabelValueReferenceObject::new(
                $germanData->UniqueId,
                Label::new(
                    $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                    $germanData->Title
                )
            );
        }
        return $dataList;
    }

    private function queryCantonsPlaces(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $places = $this->queryPlaces('GetListGemeinde', ['GetListGemeindeResult']);

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::GERMAN, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::ENGLISH, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {
            $obj = new stdClass();
            $obj->id = $germanData->UniqueId;
            $obj->label = Label::new(
                $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                $germanData->Title
            );
            if (array_key_exists($obj->id, $places)) {
                $obj->places = $places[$obj->id];
            }

            $dataList[] = $obj;

        }
        return $dataList;
    }

    private function queryCountriesCantonsPlaces(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $cantonsPlaces = $this->queryCantonsPlaces('GetListKanton', ['GetListKantonResult']);


        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::GERMAN, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::ENGLISH, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {
            $obj = new stdClass();
            $obj->id = $germanData->UniqueId;
            $obj->label = Label::new(
                $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                $germanData->Title
            );

            if ($obj->id === 30532) {
                $obj->cantons = $cantonsPlaces; //todo work with references
            } // SWITZERLAND

            $dataList[] = $obj;


        }
        return $dataList;
    }

    private function queryHelpTable(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::GERMAN, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::ENGLISH, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {
            $dataList[] = LabelValueReferenceObject::new(
                $germanData->UniqueId,
                Label::new(
                    $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                    $germanData->Title
                )
            );
        }
        return $dataList;
    }

    private function queryCertificateTypes(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $certificates = $this->queryCertificates('GetListStudienberechtigungsausweis', ['GetListStudienberechtigungsausweisResult']);

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::GERMAN, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::ENGLISH, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {


            $obj = new stdClass();
            $obj->id = $germanData->UniqueId;
            $obj->label = Label::new(
                $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                $germanData->Title
            );
            $obj->{"min-issue-date"} = 1958; //todo
            $obj->{"max-issue-date"} = 2008; //todo
            $obj->DomicileRequired = $germanData->WohngemeindeErforderlich;

            if (array_key_exists($obj->id, $certificates)) {
                $obj->certificates = $certificates[$obj->id];
            }

            $dataList[] = $obj;
        }
        return $dataList;
    }

    private function queryCertificates(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        //$cantonsWithSchoolsByCertificate = $this->queryCantonsWithSchoolsByCertificate('GetListSchuleMaturitaet', ['GetListSchuleMaturitaetResult']);

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::GERMAN, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::ENGLISH, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {

            $dataList[] = LabelValueReferenceObject::new(
                $germanData->UniqueId,
                Label::new(
                    $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                    $germanData->Title
                )
            );

            /*
            $obj = new stdClass();
            $obj->id = $germanData->UniqueId;
            $obj->label = Label::new(
                $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                $germanData->Title
            );
            $obj->{"min-issue-date"} = $germanData->GueltigAb;
            $obj->{"max-issue-date"} = $germanData->GueltigBis;
            $obj->certificateTypeUniqueId = $germanData->TypUniqueId;

            if (array_key_exists($obj->id, $cantonsWithSchoolsByCertificate)) {
                $obj->cantons = $cantonsWithSchoolsByCertificate[$obj->id];
            }

            $dataList[$obj->certificateTypeUniqueId][] = $obj;
            */
        }

        return $dataList;
    }

    private function queryQualifications()
    {
        $operationName = "GetListVoraussetzungen";
        $client = $this->getClient(SoapFile::HELPTABLE->toPath()); //todo;
        $germanDataList =$this->queryDatalist($client, $operationName, ['GetListVoraussetzungenResult','ObjVoraussetzung'],LanguageCode::GERMAN);
        $englishDatalist = $this->queryDatalist($client, $operationName, ['GetListVoraussetzungenResult','ObjVoraussetzung'],LanguageCode::ENGLISH);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDataList as $index => $germanData) {
            $obj = new stdClass();
            $obj->id = $germanData->UniqueId;
            $obj->label = Label::new(
                $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                $germanData->Title
            );
            $obj->qualifications = null;

            $dataList[$germanData->{'StudyLevelUniqueId'}][] = $obj;


        }
        return $dataList;
    }


    private function querySubjects(): array
    {
        $combinations = $this->querySubjectCombinations();
        $operationName = "GetListStudiengangsversion";
        $operationResultName = $operationName . "Result";
        $parameters['pApplication'] = 1;
        $client = $this->getClient(SoapFile::DEGREE_PROGRAMME->toPath());
        $dataList = [];
        foreach ($this->degreeProgramSubjectFilter as $subjectFilter) {

            foreach ($subjectFilter->bfsCodes as $bfsCode) {
                $parameters['pBfsCodeStufe'] = $bfsCode;

                $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::GERMAN, $parameters)->$operationResultName);
                $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::ENGLISH, $parameters)->$operationResultName);

                foreach ($germanDataList as $index => $subject) {

                    $subjectCombinations = [];
                    if (array_key_exists($subject->UniqueId, $combinations) === true) {
                        $subjectCombinations = $combinations[$subject->UniqueId];
                    }

                    $dataList[$subjectFilter->uniqueId][] = Subject::new(
                        $subject->{'UniqueId'},
                        Label::new(
                            $englishDataList[$index]->{'Title'},
                            $subject->{'Title'},
                        ),
                        $subject->{"Ects"},
                        $subjectCombinations
                    );
                }

            }
        }


        return $dataList;
    }

    private function querySubjectCombinations(): array
    {
        $combinations = $this->queryCombinations();

        $operationName = "getListStudienstruktur";
        $operationResultName = "GetListStudienstrukturResult";
        $client = $this->getClient(SoapFile::DEGREE_PROGRAMME->toPath());
        $parameters['pApplication'] = 1;

        $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::GERMAN, $parameters)->$operationResultName);
        $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::ENGLISH, $parameters)->$operationResultName);

        $dataList = [];
        foreach ($germanDataList as $index => $object) {
            $subjectCombinations = [
                MandatoryType::MANDATORY->value => [],
                ChoiceType::SINGLE_CHOICE->value => [],
                ChoiceType::MULTIPLE_CHOICE->value => []
            ];
            if (array_key_exists($object->UniqueId, $combinations) == true) {
                $subjectCombinations[MandatoryType::MANDATORY->value] = $combinations[$object->UniqueId][MandatoryType::MANDATORY->value];
                $subjectCombinations[ChoiceType::SINGLE_CHOICE->value] = $combinations[$object->UniqueId][ChoiceType::SINGLE_CHOICE->value];
                $subjectCombinations[ChoiceType::MULTIPLE_CHOICE->value] = $combinations[$object->UniqueId][ChoiceType::MULTIPLE_CHOICE->value];
            }


            $dataList[$object->StudiengangsversionUniqueId][] = SubjectCombination::new(
                $object->UniqueId,
                Label::new(
                    $englishDataList[$index]->Title,
                    $object->Title,
                ),
                $subjectCombinations[MandatoryType::MANDATORY->value],
                $subjectCombinations[ChoiceType::SINGLE_CHOICE->value],
                $subjectCombinations[ChoiceType::MULTIPLE_CHOICE->value]
            );
        }
        return $dataList;
    }

    private function extractSubjectCombinations(array $degreeProgramsSubjects): object
    {
        $combinations = new \stdClass();
        foreach ($degreeProgramsSubjects as $deegreeProgramSubjects) {
            foreach ($deegreeProgramSubjects as $deegreeProgramSubject) {
                foreach ($deegreeProgramSubject->combinations as $combination) {
                    $combinations->{$combination->id} = $combination->jsonSerialize();
                }
            }
        }
        return $combinations;
    }

    private function queryCombinations()
    {
        $mandatoryChoices = $this->queryMandatoryChoices();
        $singleChoices = [];
        $multipleChoices = $this->querySubjectChoices(true);

        $operationName = "GetListStrukturStudienprogramm";
        $operationResultName = $operationName . "Result";
        $client = $this->getClient(SoapFile::DEGREE_PROGRAMME->toPath());

        $parameters['pApplication'] = 1;

        $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::GERMAN, $parameters)->$operationResultName);
        $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::ENGLISH, $parameters)->$operationResultName);

        $dataList = [];
        foreach ($germanDataList as $index => $object) {

            $mandatorySubjectChoices = [];
            if (array_key_exists($object->UniqueId, $mandatoryChoices)) {
                $mandatorySubjectChoices = $mandatoryChoices[$object->UniqueId];
            }
            $singleSubjectChoices = [];
            if (array_key_exists($object->UniqueId, $singleChoices)) {
                $singleSubjectChoices = $singleChoices[$object->UniqueId];
            }
            $multipleSubjectChoices = [];
            if (array_key_exists($object->UniqueId, $multipleChoices)) {
                $multipleSubjectChoices = $multipleChoices[$object->UniqueId];
            }

            $dataList[$object->StudienstrukturUniqueId][MandatoryType::MANDATORY->value] = $mandatorySubjectChoices;
            $dataList[$object->StudienstrukturUniqueId][ChoiceType::SINGLE_CHOICE->value] = $singleSubjectChoices;
            $dataList[$object->StudienstrukturUniqueId][ChoiceType::MULTIPLE_CHOICE->value] = $multipleSubjectChoices;
        }
        return $dataList;
    }

    private function queryMandatoryChoices()
    {
        $operationName = "GetListStrukturStudienprogramm";
        $operationResultName = $operationName . "Result";
        $client = $this->getClient(SoapFile::DEGREE_PROGRAMME->toPath());

        $parameters['pApplication'] = 1;
        $parameters['pPflichttyp'] = 1;

        $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::GERMAN, $parameters)->$operationResultName);
        $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::ENGLISH, $parameters)->$operationResultName);

        $dataList = [];
        foreach ($germanDataList as $index => $object) {
            if (count($object->ListObjStudienprogramm) > 0) {
                foreach ($object->ListObjStudienprogramm as $subjectChoiceIndex => $subjectChoice) {
                    $dataList[$object->UniqueId][] = Choice::new(
                        $subjectChoice->UniqueId,
                        Label::new(
                            $englishDataList[$index]->ListObjStudienprogramm[$subjectChoiceIndex]->Fach,
                            $subjectChoice->Fach,
                        ),
                        $subjectChoice->Ects
                    );
                }
            }
        }
        return $dataList;
    }

    private function querySubjectChoices($choiceTypeMultiple = false)
    {
        $operationName = "GetListStrukturStudienprogramm";
        $operationResultName = $operationName . "Result";
        $client = $this->getClient(SoapFile::DEGREE_PROGRAMME->toPath());

        $parameters['pApplication'] = 1;

        $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::GERMAN, $parameters)->$operationResultName);
        $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::ENGLISH, $parameters)->$operationResultName);

        $dataList = [];
        foreach ($germanDataList as $index => $object) {
            if ($object->Pflichttyp === "Pflicht") {
                continue;
            }
            /*if($choiceTypeMultiple === false) {
                if($object->DisplayMultichoice === 1) {
                    continue;
                }
            }
            if($choiceTypeMultiple === true) {
                if($object->DisplayMultichoice !== 1) {
                    continue;
                }
            }*/

            if (count($object->ListObjStudienprogramm) > 0) {
                $choices = [];
                foreach ($object->ListObjStudienprogramm as $subjectChoiceIndex => $subjectChoice) {
                    $choices[] = Choice::new(
                        $subjectChoice->UniqueId,
                        Label::new(
                            $englishDataList[$index]->ListObjStudienprogramm[$subjectChoiceIndex]->Fach,
                            $subjectChoice->Fach,
                        ),
                        $subjectChoice->Ects
                    );
                }
                $dataList[$object->UniqueId][] = SubjectChoice::new(
                    $object->UniqueId,
                    Label::new(
                        $englishDataList[$index]->Description,
                        $object->Description,
                    ),
                    $object->Ects,
                    $choices
                );

            }
        }
        return $dataList;
    }

    private function query($client, string $operationName, LanguageCode $languageCode, array $additionalParameters = [])
    {
        $parameters = SoapParameters::new(
            $this->soapServerHost,
            $this->soapCredentials,
            $languageCode
        )->parameters;

        if (count($additionalParameters) > 0) {
            $parameters = array_merge($parameters, $additionalParameters);
        }

        return $client->$operationName($parameters);
    }

    public function writeReferenceObject(ReferenceObjectName $referenceObjectName, array|object $referenceObject): void
    {
        $filePath = $this->configFilesDirectoryPath . "/reference-objects/" . $referenceObjectName->value . ".json";
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $jsonDocument = fopen($filePath, "w");
        fwrite($jsonDocument, json_encode($referenceObject, JSON_PRETTY_PRINT));
    }

    private function getClient(string $wsdlFilePath): \SoapClient
    {
        try {
            return new \SoapClient($this->soapWsdlServer->toString() . "/" . $wsdlFilePath,
                ['connection_timeout' => '1']);
        } catch (SOAPFault $e) {
            print_r($e->faultstring);
        }
    }

    /**
     * @param \SoapClient $client
     * @param string $operationName
     * @param array $operationResultNames
     * @return mixed
     */
    private function queryDatalist(\SoapClient $client, string $operationName, array $operationResultNames, LanguageCode $languageCode, array $additionalParameters = []): mixed
    {
        $dataList = $this->query($client, $operationName, $languageCode, $additionalParameters);
        foreach ($operationResultNames as $resultName) {
            $dataList = $dataList->$resultName;
        }
        if (is_string($dataList)) {
            $dataList = json_decode($dataList);
        }
        return $dataList;
    }


}