<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Repositories;

use DateTime;
use stdClass;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\DegreeProgramConfig;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\SoapFile;
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\SoapParameters;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\OutgoingMessages\{
    Message,
    MessageName,
    CreateReferenceObject
};
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities\{EntityKey,
    EntityPropertyKey,
    EntityReferenceKey,
    LabelValueReferenceObject,
    DegreeProgramme,
    DegreeProgrammeType,
    Subject,
    SubjectChoice,
    SubjectCombination,
    Choice
};
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\{KeyValueCollection,
    KeyValue,
    LocalizedStringValue,
    ObjectId,
    ObjectProperty,
    Server,
    Credentials,
    Label,
    MandatoryType,
    ChoiceType,
    UniqueValueCollection,
    ValueObjectName,
    Year,
    CertificateTypeIssueYearRange
};
use JsonSerializable;
use SoapFault;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\LanguageCode;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\PropertyType;

class EnrolmentConfigurationReferenceObjectRepository
{
    private \WeakMap $storage;

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
        $this->storage = new \WeakMap();
    }

    public static function new(string $configFilesDirectoryPath, Server $soapWsdlServer, string $soapServerHost, Credentials $soapCredentials, array $degreeProgramSubjectFilter): self
    {
        return new self(...get_defined_vars());
    }

    public function get(ObjectType $referenceObjectName)
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
            ObjectType::SUBJECTS => $this->createSubjects(),
            ObjectType::DEGREE_PROGRAMS => $this->createDegreeProgrammes(),
            ObjectType::COUNTRIES => $this->createCountries(),
            ObjectType::SEMESTERS => $this->createSemesters(),
            ObjectType::MIN_PASSWORD_LENGTH => [],
            ObjectType::LANGUAGE => [],
            ObjectType::SALUTATIONS => $this->createSalutations(),
            ObjectType::AREA_CODES => $this->createAreaCodes(),
            ObjectType::CANTONS => $this->createCantons(),
            ObjectType::CERTIFICATE_TYPES => [],
            ObjectType::GRADUATION_TYPES => $this->createGraduationTypes(),
            ObjectType::CERTIFICATES => [],
            ObjectType::CERTIFICATES_ISSUE_YEARS => [],
            ObjectType::PHOTO_TYPE => [],
            ObjectType::PLACES => $this->createPlaces(),
            ObjectType::SCHOOLS => $this->createSchools(),
            ObjectType::CHOICE_SUBJECT => [],
            ObjectType::COMPLETED => [],
            ObjectType::IDENTIFICATION_NUMBER => [],
            ObjectType::INTENDED_DEGREE_PROGRAM => [],
            ObjectType::INTENDED_DEGREE_PROGRAM_2 => [],
            ObjectType::LEGAL => [],
            ObjectType::PORTRAIT => [],
            ObjectType::UNIVERSITY_ENTRANCE_QUALIFICATION => [],
            ObjectType::BASE_DATA => [],
            ObjectType::SUBJECT_COMBINATIONS => [],
            ObjectType::LANGUAGES => []
        };
    }

    private function createCountries(): void
    {
        $list = $this->queryHelpTable('GetListStaat', ['GetListStaatResult']);
        $this->writeReferenceObject(ObjectType::COUNTRIES, $list);
    }

    private function createCantons(): void
    {
        $list = $this->queryCantons('GetListKanton', ['GetListKantonResult']);
        $this->writeReferenceObject(ObjectType::CANTONS, $list);
    }

    private function createSchools(): void
    {
        $list = $this->querySchools('GetListSchuleMaturitaet', ['GetListSchuleMaturitaetResult']);
        $this->writeReferenceObject(ObjectType::SCHOOLS, $list);
    }

    private function createPlaces(): void
    {
        $list = $this->queryPlaces('GetListGemeinde', ['GetListGemeindeResult']);
        $this->writeReferenceObject(ObjectType::PLACES, $list);
    }


    private function createGraduationTypes(): void
    {
        $list = $this->queryHelpTable('GetListAbschlusstyp', ['GetListAbschlusstypResult', 'ObjAbschlusstyp']);
        $this->writeReferenceObject(ObjectType::GRADUATION_TYPES, $list);
    }


    private function createSubjects(): void
    {
        $subjects = $this->querySubjects();
        $this->writeReferenceObject(ObjectType::SUBJECTS, $subjects);

        $this->writeReferenceObject(ObjectType::SUBJECT_COMBINATIONS, $this->extractSubjectCombinations($subjects));
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
        $this->writeReferenceObject(ObjectType::DEGREE_PROGRAMS, $dataList);
    }

    private function createSalutations()
    {
        $salutations = $this->queryHelpTable('GetListAnrede', ['GetListAnredeResult', 'ObjListAnrede']);
        $this->writeReferenceObject(ObjectType::SALUTATIONS, $salutations);
    }

    private function createAreaCodes()
    {
        $places = $this->queryHelpTable('GetListOrtschaft', ['GetListOrtschaftResult', 'objListOrtschaft']);
        $areaCodes = [];

        $this->writeReferenceObject(ObjectType::AREA_CODES, $places);
    }

    private function createSemesters()
    {
        $semesters = $this->queryHelpTable('GetListSemester', ['GetListSemesterResult']);
        $this->writeReferenceObject(ObjectType::SEMESTERS, $semesters);
    }

    private function querySchools(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::DE, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::EN, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }


        $handledSchools = [];
        $dataList = [];

        foreach ($germanDatalist as $germanData) {

            if (in_array($germanData->SchuleUniqueId, $handledSchools)) {
                continue;
            }
            $handledSchools[] = $germanData->SchuleUniqueId;

            $obj = new stdClass();
            $obj->id = (string)$germanData->SchuleUniqueId;
            $obj->label = Label::new(
                $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                $germanData->Title
            );
            $dataList[] = $obj;

            /*
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
             }*/

        }

        return $dataList;
    }

    private function queryPlaces(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::DE, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::EN, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {
            $obj = new stdClass();
            $obj->id = (string)$germanData->UniqueId;
            $obj->label = Label::new(
                $englishDataByUniqueIdList[$germanData->UniqueId]->Title,
                $germanData->Title
            );
            $obj->cantonId = $germanData->KantonUniqueId;
            $dataList[] = $obj;
        }
        return $dataList;
    }

    private function queryCantons(string $operationName, array $operationResultName, array $additionalParameters = [])
    {
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::DE, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::EN, $additionalParameters);

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

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::DE, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::EN, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {
            $obj = new stdClass();
            $obj->id = (string)$germanData->UniqueId;
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


        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::DE, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::EN, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDatalist as $germanData) {
            $obj = new stdClass();
            $obj->id = (string)$germanData->UniqueId;
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

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::DE, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::EN, $additionalParameters);

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

    public function queryCertificateTypes(): KeyValueCollection
    {
        $operationName = 'GetListStudienberechtigungsausweistyp';
        $operationResultName = ['GetListStudienberechtigungsausweistypResult'];
        $additionalParameters = ['pType' => 1];
        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::DE, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::EN, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }

        $keyValues = [];
        foreach ($germanDatalist as $germanData) {
            $entityKey = EntityKey::new($germanData->UniqueId, ObjectType::CERTIFICATE_TYPE);

            $this->applyEntityPropertyLoaded(EntityPropertyKey::new($entityKey, PropertyType::LABEL), UniqueValueCollection::fromArray([
                KeyValue::new(LanguageCode::DE->value, $germanData->Title),
                KeyValue::new(LanguageCode::EN->value, $englishDataByUniqueIdList[$germanData->UniqueId]->Title),
            ]));

            $certificateIds = [];
            foreach ($this->getEntities(ObjectType::CERTIFICATE) as $certificate) {
                if ($certificate->contains(PropertyType::CERTIFICATE_TYPE_ID->value)) {
                    $certificateIds[$certificate->get(PropertyType::CERTIFICATE_TYPE_ID->value)] = $certificate->get(PropertyType::CERTIFICATE_TYPE_ID->value);
                }
            }
            $this->applyEntityPropertyLoaded(EntityPropertyKey::new($entityKey, PropertyType::CERTIFICATE_IDS), $certificateIds);

            $keyValues[] = KeyValue::new($entityKey->entityId, $this->storage[$entityKey]);
        }

        return KeyValueCollection::fromArray($keyValues);
    }

    private function getEntityPropertyValue(EntityPropertyKey $entityPropertyKey): mixed
    {
        if ($this->storage->offsetExists($entityPropertyKey->entityKey) === false) {
            return $this->loadEntities($entityPropertyKey->entityKey->entityType)->get($entityPropertyKey->entityKey->entityId)->get($entityPropertyKey->propertyType->value);
        }
        return $this->storage[$entityPropertyKey->entityKey]->get($entityPropertyKey->entityKey->entityId)->get($entityPropertyKey->propertyType->value);
    }

    private function loadCertificateIdsForCertificateType(): KeyValueCollection
    {

        $keyValues = [];
        foreach ($this->getEntities(ObjectType::CERTIFICATE)->toArray() as $certificate) {
            $certificateTypeId = $this->getEntityPropertyValue(EntityPropertyKey::new(ObjectType::CERTIFICATE, PropertyType::CERTIFICATE_TYPE_ID), $certificate->entityId);
            $keyValues[] = KeyValue::new($certificateTypeId, $certificate->entityId);
        }
        return KeyValueCollection::fromArray($keyValues);
    }

    private function applyEntityPropertyLoaded(EntityPropertyKey $entityPropertyKey, mixed $value): void
    {
        $this->storage[$entityPropertyKey] = $value;

        $this->storage[$entityPropertyKey->entityKey] = KeyValueCollection::fromArray([
            KeyValue::new(
                $entityPropertyKey->propertyType->value, $this->storage[$entityPropertyKey]
            )
        ]);

        $this->storage[$entityPropertyKey->entityKey->entityType] = KeyValueCollection::fromArray([
            KeyValue::new(
                $entityPropertyKey->entityKey->entityId, $this->storage[$entityPropertyKey->entityKey]
            )
        ]);
    }


    private function queryCertificates(): KeyValueCollection
    {
        $operationName = 'GetListStudienberechtigungsausweis';
        $operationResultName = ['GetListStudienberechtigungsausweisResult'];
        $additionalParameters = [];

        $client = $this->getClient(SoapFile::HELPTABLE->toPath());

        //$cantonsWithSchoolsByCertificate = $this->queryCantonsWithSchoolsByCertificate('GetListSchuleMaturitaet', ['GetListSchuleMaturitaetResult']);

        $germanDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::DE, $additionalParameters);
        $englishDatalist = $this->queryDatalist($client, $operationName, $operationResultName, LanguageCode::EN, $additionalParameters);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $keyValueItems = [];
        foreach ($germanDatalist as $germanData) {
            $obj = new stdClass();

            $entityKey = EntityKey::new($germanData->UniqueId, ObjectType::CERTIFICATE);

            $this->applyEntityPropertyLoaded(EntityPropertyKey::new($entityKey, PropertyType::LABEL), UniqueValueCollection::fromArray([
                KeyValue::new(LanguageCode::DE->value, $germanData->Title),
                KeyValue::new(LanguageCode::EN->value, $englishDataByUniqueIdList[$germanData->UniqueId]->Title),
            ]));
            $this->applyEntityPropertyLoaded(EntityPropertyKey::new($entityKey, PropertyType::ISSUE_PERIOD), CertificateTypeIssueYearRange::new($germanData->GueltigAb, $germanData->GueltigBis));
            $this->applyEntityPropertyLoaded(EntityPropertyKey::new($entityKey, PropertyType::CERTIFICATE_TYPE_ID), $germanData->TypUniqueId);

            $keyValueItems[] = KeyValue::new($entityKey->entityId, $this->storage[$entityKey]);
        }

        return KeyValueCollection::fromArray($keyValueItems);
    }


    public function getEntities(ObjectType $objectType): KeyValueCollection
    {
        return $this->storage[$objectType] ??= $this->loadEntities($objectType);
    }

    /**
     * @param ObjectType $objectType
     * @return EntityKey[] array
     */
    private function loadEntities(ObjectType $objectType): KeyValueCollection
    {
        return match ($objectType) {
            ObjectType::CERTIFICATE_TYPE => $this->queryCertificateTypes(),
            ObjectType::CERTIFICATE => $this->queryCertificates()
        };
    }

    private function queryQualifications()
    {
        $operationName = "GetListVoraussetzungen";
        $client = $this->getClient(SoapFile::HELPTABLE->toPath()); //todo;
        $germanDataList = $this->queryDatalist($client, $operationName, ['GetListVoraussetzungenResult', 'ObjVoraussetzung'], LanguageCode::DE);
        $englishDatalist = $this->queryDatalist($client, $operationName, ['GetListVoraussetzungenResult', 'ObjVoraussetzung'], LanguageCode::EN);

        $englishDataByUniqueIdList = [];
        foreach ($englishDatalist as $englishData) {
            $englishDataByUniqueIdList[$englishData->UniqueId] = $englishData;
        }
        $dataList = [];
        foreach ($germanDataList as $index => $germanData) {
            $obj = new stdClass();
            $obj->id = (string)$germanData->UniqueId;
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

                $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::DE, $parameters)->$operationResultName);
                $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::EN, $parameters)->$operationResultName);

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

        $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::DE, $parameters)->$operationResultName);
        $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::EN, $parameters)->$operationResultName);

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

        $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::DE, $parameters)->$operationResultName);
        $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::EN, $parameters)->$operationResultName);

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

        $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::DE, $parameters)->$operationResultName);
        $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::EN, $parameters)->$operationResultName);

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

        $germanDataList = json_decode($this->query($client, $operationName, LanguageCode::DE, $parameters)->$operationResultName);
        $englishDataList = json_decode($this->query($client, $operationName, LanguageCode::EN, $parameters)->$operationResultName);

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
                $dataList[(string)$object->UniqueId][] = SubjectChoice::new(
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

    public function writeReferenceObject(ObjectType $referenceObjectName, array|object $referenceObject): void
    {
        $filePath = $this->configFilesDirectoryPath . "/reference-objects/" . $referenceObjectName->value . ".json";
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $jsonDocument = fopen($filePath, "w");
        fwrite($jsonDocument, json_encode($referenceObject));
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