<?php

namespace FluxEco\UnibeOmnitrackerClient;

use FluxEco\UnibeOmnitrackerClient\Types;
use FluxEco\UnibeOmnitrackerClient\Types\Exceptions\FluxEcoUnibeOmnitrackerClientFluxEcoInvalidInputException;
use FluxEco\UnibeOmnitrackerClient\Types\UnibeOmnitrackerSoapApi\BaseDataItemAttributesDefinition;
use FluxEcoType\FluxEcoActionDefinition;
use FluxEcoType\FluxEcoExceptionDefinitions\FluxEcoInvalidInputException;
use stdClass;

//todo - create the response objects by mapping and described unibe omnitracker client & unibe omnitracker api description

final readonly class Api
{
    const MANDATORY_TYPE_MANDATORY = "mandatory";
    const CHOICE_TYPE_SINGLE_CHOICE = "single-choice";
    const CHOICE_TYPE_MULTIPLE_CHOICE = "multiple-choice";

    private function __construct(
        public Config $config
    )
    {

    }

    public static function new()
    {
        return new self(Config::new());
    }

    public function readBaseDataItemAttributesDefinition(): BaseDataItemAttributesDefinition
    {
        return $this->config->baseDataItemDefinition;
    }

    /**
     * @throws FluxEcoInvalidInputException
     */
    public function createEnrolment(string $transactionId, string $password): object
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->CreateBasisdaten;
        /** @var Types\UnibeOmnitrackerSoapApi\CreateBasisdatenParametersDefinition $createBasisDatenParametersDefinition */
        $createBasisDatenParametersDefinition = $actionDefinition->parametersDefinition;

        $parametersObject = new stdClass();
        $parametersObject->{$createBasisDatenParametersDefinition->pSessionId->name} = $transactionId;
        $parametersObject->{$createBasisDatenParametersDefinition->pUserPassword->name} = $password;
        $parametersObject = $this->hydrateObject($parametersObject, $createBasisDatenParametersDefinition->defaultParametersDefinition);

        return $this->processOmnitrackerApiRequest($actionDefinition, $parametersObject, $transactionId);
    }

    /**
     * @throws FluxEcoInvalidInputException
     */
    public function updateEnrolment(string $transactionId, object $baseDataItem): object
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->UpdateBasisdaten;
        /** @var Types\UnibeOmnitrackerSoapApi\UpdateBasisdatenParametersDefinition $updateBasisDatenParametersDefinition */
        $updateBasisDatenParametersDefinition = $actionDefinition->parametersDefinition;

        $parametersObject = new stdClass();
        $parametersObject->{$updateBasisDatenParametersDefinition->pSessionId->name} = $transactionId;
        $parametersObject->{$updateBasisDatenParametersDefinition->pObjBasisdaten->name} = $baseDataItem;
        $parametersObject = $this->hydrateObject($parametersObject, $updateBasisDatenParametersDefinition->defaultParametersDefinition);

        return $this->processOmnitrackerApiRequest($actionDefinition, $parametersObject, $transactionId);
    }

    private function createAbsoluteActionPath(FluxEcoActionDefinition $actionDefinition): string
    {
        return $this->config->createAbsoluteActionPath($this->config->settings->unibeOmnitrackerSoapApiBindingDefinition->toString(), $actionDefinition->path);
    }

    private function hydrateObject(
        object                                                    $parametersObject,
        Types\UnibeOmnitrackerSoapApi\DefaultParametersDefinition $defaultParametersDefinition
    ): object
    {
        $parametersObject->{$defaultParametersDefinition->pOTUser->name} = $this->config->settings->omnitrackerCredentials->userName;
        $parametersObject->{$defaultParametersDefinition->pOTPassword->name} = $this->config->settings->omnitrackerCredentials->password;
        $parametersObject->{$defaultParametersDefinition->pOTServer->name} = $this->config->settings->omnitrackerServerHost;
        $parametersObject->{$defaultParametersDefinition->pLanguagecode->name} = $this->config->settings->defaultLanguageCode;
        return $parametersObject;
    }

    /**
     * @return object[]
     */
    public function readSalutations(): array
    {
        return json_decode(json_encode($this->readIdLabelDataList($this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListAnrede->name)));
    }

    /**
     * @return object[]
     */
    public function readSemesters(): array
    {
        return json_decode(json_encode($this->readIdLabelDataList($this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListSemester->name)));
    }

    /**
     * @return object[]
     */
    public function readMotherLanguage(): array
    {
        return json_decode(json_encode($this->readIdLabelDataList($this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListMuttersprache->name)));
    }

    /**
     * @return object[]
     */
    public function readCorrespondenceLanguage(): array
    {
        return json_decode(json_encode($this->readIdLabelDataList($this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListKorrespondenzsprache->name)));
    }

    /**
     * @return object[]
     */
    public function readSubjects(): array
    {
        return $this->readSubjectsAndSubjectCombinations();
    }

    /**
     * @return object[]
     */
    public function readSubjectCombinations(): array
    {
        $subjectsAndCombinations = $this->readSubjectsAndSubjectCombinations();
        $combinations = [];
        foreach ($subjectsAndCombinations as $deegreeProgramSubjects) {
            foreach ($deegreeProgramSubjects as $deegreeProgramSubject) {
                foreach ($deegreeProgramSubject->combinations as $combination) {
                    $combinations[$combination->id] = $combination->jsonSerialize();
                }
            }
        }
        return $combinations;
    }


    /**
     * @return array
     */
    private function readSubjectsAndSubjectCombinations(): array
    {
        $combinations = $this->readStudyProgrammeSubjectCombinations();
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListStudiengangsversion;
        /** @var Types\UnibeOmnitrackerSoapApi\GetListStudiengangsversionParametersDefinition $parametersDefinition */
        $parametersDefinition = $actionDefinition->parametersDefinition;
        $parameters = new stdClass();
        $parameters->{$parametersDefinition->pApplication->name} = 1;
        $parameters = $this->hydrateObject($parameters, $this->config->settings->defaultActionParameterDefinitions);

        $dataList = [];
        foreach ($this->config->settings->degreeProgramSubjectFilter as $subjectFilter) {
            foreach ($subjectFilter->bfsCodes as $bfsCode) {
                $parameters->{$parametersDefinition->pBfsCodeStufe->name} = $bfsCode;
                $results = $this->processOmnitrackerApiRequest($actionDefinition, $parameters);

                foreach ($results as $index => $subject) {

                    $subjectCombinations = [];
                    if (array_key_exists($subject->UniqueId, $combinations) === true) {
                        $subjectCombinations = $combinations[$subject->UniqueId];
                    }

                    $dataList[$subjectFilter->uniqueId][] = Types\ResponseData\Subject::new(
                        $subject->{'UniqueId'},
                        Types\ResponseData\Label::newGermanLabel(
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


    /**
     * @return array
     */
    private function readStudyProgrammeSubjectCombinations(): array
    {
        $combinations = $this->readCombinations();

        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->getListStudienstruktur;
        /** @var Types\UnibeOmnitrackerSoapApi\GetListStrukturParametersDefinition $parametersDefinition */
        $parametersDefinition = $actionDefinition->parametersDefinition;

        $parameters = new stdClass();
        $parameters->{$parametersDefinition->pApplication->name} = 1;
        $parameters = $this->hydrateObject($parameters, $this->config->settings->defaultActionParameterDefinitions);
        $results = $this->processOmnitrackerApiRequest($actionDefinition, $parameters);


        $dataList = [];
        foreach ($results as $index => $object) {
            $subjectCombinations = [
                self::MANDATORY_TYPE_MANDATORY => [],
                self::CHOICE_TYPE_SINGLE_CHOICE => [],
                self::CHOICE_TYPE_MULTIPLE_CHOICE => []
            ];
            if (array_key_exists($object->UniqueId, $combinations)) {
                $subjectCombinations[self::MANDATORY_TYPE_MANDATORY] = $combinations[$object->UniqueId][self::MANDATORY_TYPE_MANDATORY];
                $subjectCombinations[self::CHOICE_TYPE_SINGLE_CHOICE] = $combinations[$object->UniqueId][self::CHOICE_TYPE_SINGLE_CHOICE];
                $subjectCombinations[self::CHOICE_TYPE_MULTIPLE_CHOICE] = $combinations[$object->UniqueId][self::CHOICE_TYPE_MULTIPLE_CHOICE];
            }
            $dataList[$object->StudiengangsversionUniqueId][] = Types\ResponseData\SubjectCombination::new(
                $object->UniqueId,
                Types\ResponseData\Label::newGermanLabel(
                    $object->Title,
                ),
                $subjectCombinations[self::MANDATORY_TYPE_MANDATORY],
                $subjectCombinations[self::CHOICE_TYPE_SINGLE_CHOICE],
                $subjectCombinations[self::MANDATORY_TYPE_MANDATORY]
            );
        }
        return $dataList;
    }

    /**
     * @return array
     * [StudienstrukturUniqueId => ["mandatory" => Choice[], "single-choice" => SubjectChoice[],  "multiple-choice" => SubjectChoice[]]
     */
    private function readCombinations()
    {
        $mandatoryChoices = $this->readMandatoryChoices();
        $singleChoices = []; //we work only with multiple choice
        $multipleChoices = $this->readSubjectChoices();

        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListStrukturStudienprogramm;
        /** @var Types\UnibeOmnitrackerSoapApi\GetListStrukturStudienprogrammParametersDefinition $parametersDefinition */
        $parametersDefinition = $actionDefinition->parametersDefinition;
        $parameters = new stdClass();
        $parameters->{$parametersDefinition->pApplication->name} = 1;
        $parameters = $this->hydrateObject($parameters, $this->config->settings->defaultActionParameterDefinitions);
        $results = $this->processOmnitrackerApiRequest($actionDefinition, $parameters);

        $dataList = [];
        foreach ($results as $index => $object) {
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

            //todo - do we need this - in this way
            $dataList[$object->StudienstrukturUniqueId][self::MANDATORY_TYPE_MANDATORY] = $mandatorySubjectChoices;
            $dataList[$object->StudienstrukturUniqueId][self::CHOICE_TYPE_SINGLE_CHOICE] = $singleSubjectChoices;
            $dataList[$object->StudienstrukturUniqueId][self::CHOICE_TYPE_MULTIPLE_CHOICE] = $multipleSubjectChoices;
        }
        return $dataList;
    }

    /**
     * @return array
     * [ListObjStudienprogrammUniqueID => Choice[]]
     */
    private function readMandatoryChoices()
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListStrukturStudienprogramm;
        /** @var Types\UnibeOmnitrackerSoapApi\GetListStrukturStudienprogrammParametersDefinition $parametersDefinition */
        $parametersDefinition = $actionDefinition->parametersDefinition;

        $parameters = new stdClass();
        $parameters->{$parametersDefinition->pApplication->name} = 1;
        $parameters->{$parametersDefinition->pPflichttyp->name} = 1;
        $parameters = $this->hydrateObject($parameters, $this->config->settings->defaultActionParameterDefinitions);

        $results = $this->processOmnitrackerApiRequest($actionDefinition, $parameters);
        $dataList = [];
        foreach ($results as $index => $object) {
            if (count($object->ListObjStudienprogramm) > 0) {
                foreach ($object->ListObjStudienprogramm as $subjectChoiceIndex => $subjectChoice) {
                    $dataList[$object->UniqueId][] = Types\ResponseData\Choice::new(
                        $subjectChoice->UniqueId,
                        Types\ResponseData\Label::newGermanLabel($subjectChoice->Fach),
                        $subjectChoice->Ects
                    );
                }
            }
        }
        return $dataList;
    }

    /**
     * @return array
     * [ListObjStudienprogrammUniqueID => SubjectChoice[]]
     */
    private function readSubjectChoices()
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListStrukturStudienprogramm;
        /** @var Types\UnibeOmnitrackerSoapApi\GetListStrukturStudienprogrammParametersDefinition $parametersDefinition */
        $parametersDefinition = $actionDefinition->parametersDefinition;
        $parameters = new stdClass();
        $parameters->{$parametersDefinition->pApplication->name} = 1;
        $parameters = $this->hydrateObject($parameters, $this->config->settings->defaultActionParameterDefinitions);

        $results = $this->processOmnitrackerApiRequest($actionDefinition, $parameters);
        $dataList = [];
        foreach ($results as $index => $object) {
            if ($object->Pflichttyp === "Pflicht") {
                continue;
            }

            $choices = [];
            foreach ($object->ListObjStudienprogramm as $subjectChoiceIndex => $subjectChoice) {
                $choices[] = Types\ResponseData\Choice::new(
                    $subjectChoice->UniqueId,
                    Types\ResponseData\Label::newGermanLabel(
                        $subjectChoice->Fach,
                    ),
                    $subjectChoice->Ects
                );
            }
            $dataList[$object->UniqueId][] = Types\ResponseData\SubjectChoice::new(
                $object->UniqueId,
                Types\ResponseData\Label::newGermanLabel(
                    $object->Description,
                ),
                $object->Ects,
                $choices
            );
        }
        return $dataList;
    }

    /**
     * @param string $actionName
     * @return Types\ResponseData\IdLabelObject[];
     */
    private function readIdLabelDataList(string $actionName): array
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->{$actionName};
        $parametersObject = new stdClass();
        $parametersObject = $this->hydrateObject($parametersObject, $this->config->settings->defaultActionParameterDefinitions);

        $results = $this->processOmnitrackerApiRequest($actionDefinition, $parametersObject);

        $dataList = [];
        foreach ($results as $result) {
            //the unibe api has a not stringent api
            if (is_array($result)) {
                foreach ($result as $item) {
                    $dataList[] = Types\ResponseData\IdLabelObject::new(
                        $item->UniqueId,
                        Types\ResponseData\Label::newGermanLabel($item->Title),
                    );
                }
                continue;
            }
            $dataList[] = Types\ResponseData\IdLabelObject::new(
                $result->UniqueId,
                Types\ResponseData\Label::newGermanLabel($result->Title),
            );
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readMunicipalities(): array
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListGemeinde;
        /** @var Types\UnibeOmnitrackerSoapApi\GetListStudiengangsversionParametersDefinition $parametersDefinition */
        $parametersDefinition = $actionDefinition->parametersDefinition;
        $parameters = new stdClass();
        $parameters = $this->hydrateObject($parameters, $this->config->settings->defaultActionParameterDefinitions);
        $results = $this->processOmnitrackerApiRequest($actionDefinition, $parameters);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\Locality::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->Plz,
                $item->KantonUniqueId
            )));
        }
        return $dataList;
    }

    public function readPlaces(): array
    {
        $actionDefinition = $this->config->settings->unibeOmnitrackerSoapApiActionsDefinitions->GetListOrtschaft;
        /** @var Types\UnibeOmnitrackerSoapApi\GetListStudiengangsversionParametersDefinition $parametersDefinition */
        $parametersDefinition = $actionDefinition->parametersDefinition;
        $parameters = new stdClass();
        $parameters = $this->hydrateObject($parameters, $this->config->settings->defaultActionParameterDefinitions);
        $results = $this->processOmnitrackerApiRequest($actionDefinition, $parameters);
        $dataList = [];
        foreach ($results as $index => $object) {
            $dataList[] = json_decode(json_encode(Types\ResponseData\Locality::new(
                $object->UniqueId,
                Types\ResponseData\Label::newGermanLabel($object->Title),
                $object->Plz,
                $object->KantonUniqueId
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readCertificateTypes(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListStudienberechtigungsausweistyp();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\CertificateType::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->WohngemeindeErforderlich
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readCertificates(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListStudienberechtigungsausweis();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\Certificate::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->GueltigAb,
                $item->GueltigBis,
                $item->TypUniqueId
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readCantons(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListKanton();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\Canton::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title)
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readSchools(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListKanton();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\School::new(
                $item->SchuleUniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->TypUniqueId,
                $item->StudienberechtigungsausweisUniqueId,
                $item->CantonUniqueId
            )));
        }
        return $dataList;
    }

    /**
     * @return object[]
     */
    public function readCountries(): array
    {
        $requestAction = $this->config->settings->unibeOmnitrackerSoapApiRequestActions->getListStaat();
        $results = $this->processOmnitrackerApiRequest($requestAction);

        $dataList = [];
        foreach ($results as $item) {
            $dataList[$item->UniqueId] = json_decode(json_encode(Types\ResponseData\Country::new(
                $item->UniqueId,
                Types\ResponseData\Label::newGermanLabel($item->Title),
                $item->Code
            )));
        }
        return $dataList;
    }

    /**
     * @throws FluxEcoInvalidInputException
     */
    private function processOmnitrackerApiRequest(
        FluxEcoActionDefinition $actionDefinition,
        object                  $actionParameters,
        string                  $transactionId = ""
    ): object|array
    {
        $actionName = $actionDefinition->name;
        $absoluteActionPath = $this->createAbsoluteActionPath($actionDefinition);
        $actionResponseDefinition = $actionDefinition->responseDefinition;
        $response = new stdClass();

        try {
            $options = [
                'connection_timeout' => 10,
            ];
            $soapClient = new \SoapClient($absoluteActionPath, $options);
            $response = $soapClient->{$actionName}($actionParameters);
        } catch (\Exception $e) {
            error_log('SOAP request failed with error: ' . $e->getMessage());
            echo 'Sorry, an error occurred while processing your request. Please try again later.';
        }

        if (property_exists($response, "pFehler",) && empty($response->{"pFehler"}) !== true) {
            throw FluxEcoUnibeOmnitrackerClientFluxEcoInvalidInputException::createForClientSideContext(
                $response->{"pFehler"},
                $transactionId,
                $actionName
            );
        }

        $response = $response->{$actionResponseDefinition->data->name};
        if (is_string($response)) {
            return json_decode($response);
        }
        return $response;
    }
}