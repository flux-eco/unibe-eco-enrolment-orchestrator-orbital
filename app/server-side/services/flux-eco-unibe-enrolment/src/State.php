<?php

namespace FluxEco\UnibeEnrolment;

use Closure;
use Exception;
use FluxEcoType\FluxEcoId;
use FluxEcoType\FluxEcoStateMonad;
use FluxEcoType\FluxEcoStateValues;
use stdClass;

final readonly class State
{
    public StateNames|stdClass $stateNames;
    private object $init;

    private function __construct(string $pageDataDirectory, string $inputOptionDataDirectory, object $inputNames, public closure $objectFromJsonFile)
    {
        /**
         * @var StateNames|object $stateNames
         */
        $stateNames = new stdClass();
        $stateNames->init = "init";
        $stateNames->create = "create";
        $stateNames->processCreate = "processCreate";
        $stateNames->identificationNumber = "identification-number";
        $stateNames->processIdentificationNumber = "processIdentificationNumber";
        $stateNames->resume = "resume";
        //todo
        $stateNames->completed = "completed";
        $stateNames->legal = "legal";
        $stateNames->processLegal = "processLegal";
        $stateNames->choiceSubject = "choice-subject";
        $stateNames->processChoiceSubject = "processChoiceSubject";
        $stateNames->idendedDegreeProgram2 = "intended-degree-program-2";
        $stateNames->processIdendedDegreeProgram2 = "processIdendedDegreeProgram2";
        $stateNames->idendedDegreeProgram = "intended-degree-program";
        $stateNames->processIdendedDegreeProgram = "processIdendedDegreeProgram";
        $stateNames->personalData = "personal-data";
        $stateNames->processPersonalData = "processPersonalData";
        $stateNames->portrait = "portrait";
        $stateNames->processPortrait = "processPortrait";
        $stateNames->previousStudies = "previous-studies";
        $stateNames->processPreviousStudies = "processPreviousStudies";
        $stateNames->universityEntranceQualification = "university-entrance-qualification";
        $stateNames->processUniversityEntranceQualification = "processUniversityEntranceQualification";
        $stateNames->storedData = "storedData";
        $this->stateNames = $stateNames;

        /**
         * @var StateData|stdClass $init
         */
        $init = new stdClass();
        $init->transactionId = FluxEcoId::newHashedUuid4()->id; //todo callable
        $init->pageDataDirectory = $pageDataDirectory;
        $init->inputOptionDataDirectory = $inputOptionDataDirectory;
        $init->inputNames = $inputNames;

        $this->init = $init;
    }

    /**
     * @return self
     */
    public static function newEmpty(string $pageDataDirectory, string $inputOptionDataDirectory, object $inputNames, closure $objectFromJsonFile): State
    {
        return new self(...get_defined_vars());
    }

    /**
     * @throws Exception
     */
    public function readTransactionStateValues(?stdClass $stateValues, callable $objectFromJsonFile): StateValues|stdClass
    {
        if ($stateValues !== null) {
            return $stateValues;
        }
        return $this->initializeFlow($objectFromJsonFile);
    }

    /**
     * @throws Exception
     */
    private function initializeFlow(callable $objectFromJsonFile): StateValues|FluxEcoStateValues|stdClass
    {
        $stateValues = $this->initStateValues();

        return FluxEcoStateMonad::of($stateValues)
            ->bind(fn(StateValues|stdClass $stateValues) => $this->create($stateValues, $objectFromJsonFile, $stateValues->data->pageDataDirectory))->stateValues;
    }

    /**
     * @throws Exception
     */
    private function create(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->create);

        $stateData = $stateValues->data;
        $stateData->content = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->create), null];
    }


    public function processData(
        StateValues|stdClass $stateValues, object $processData, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile
    ): object
    {
        return FluxEcoStateMonad::of($stateValues)
            ->bind(fn(StateValues|stdClass $stateValues) => $this->{$stateValues->currentStateName}($stateValues, $processData, $storeNewEnrolment, $updateEnrolment, $objectFromJsonFile, $stateValues->data->pageDataDirectory))->stateValues;
    }

    /**
     * @throws Exception
     */
    public function processCreate(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        //todo think about callable $objectFromJsonFile, string $pageDataDirectory -> used for next transition
        //closures at initialization of State?
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processCreate);
        $stateData = $stateValues->data;
        $stateData->enrolmentData = $storeNewEnrolment($stateValues, $stateValues->data->transactionId, $dataToProcess->data->password);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processCreate), fn($stateValues) => $this->identicationNumber($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }


    /**
     * @throws Exception
     */
    private function identicationNumber(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->identificationNumber);

        $stateData = $stateValues->data;
        $stateData->content = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->identificationNumber), null];
    }

    /**
     * @throws Exception
     */
    public function processIdentificationNumber(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory)
    {
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processIdentificationNumber);
        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processIdentificationNumber), fn($stateValues) => $this->choiceSubject($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }

    /**
     * @throws Exception
     */
    private function choiceSubject(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->choiceSubject);

        $stateData = $stateValues->data;
        $stateData->content = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->choiceSubject), null];
    }


    /**
     * @throws Exception
     */
    public function processChoiceSubject(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        /**
         * @var StateValues $stateValues
         */
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processChoiceSubject);
        //validation is made by omnitracker backend
        $stateData = $stateValues->data;
        $stateData->enrolmentData = $updateEnrolment($stateValues, $stateValues->data->transactionId, $dataToProcess->data);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processChoiceSubject), fn($stateValues) => $this->idendedDegreeProgram($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }

    /**
     * @throws Exception
     */
    private function idendedDegreeProgram(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->idendedDegreeProgram);

        //todo mandatory
        $stateData = $stateValues->data;
        $pageContent = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateData->content = $this->resolveDynamicData($stateValues, $pageContent);

        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->idendedDegreeProgram), null];
    }


    /**
     * @throws Exception
     */
    public function processIdendedDegreeProgram(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processIdendedDegreeProgram);

        //validation is made by omnitracker backend
        $stateData = $stateValues->data;
        $stateData->enrolmentData = $updateEnrolment($stateValues, $stateValues->data->transactionId, $dataToProcess->data);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        //todo
        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processIdendedDegreeProgram), fn($stateValues) => $this->idendedDegreeProgram2($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }

    /**
     * @throws Exception
     */
    private function idendedDegreeProgram2(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->idendedDegreeProgram2);

        //todo mandatory
        $stateData = $stateValues->data;
        $pageContent = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateData->content = $this->resolveDynamicData($stateValues, $pageContent);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->idendedDegreeProgram2), null];
    }

    /**
     * @throws Exception
     */
    public function processIdendedDegreeProgram2(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processIdendedDegreeProgram2);
        /**
         * @var StateData|stdClass $stateData
         */
        $stateData = $stateValues->data;

        $studyPrograms = [];
        //todo
        if(property_exists( $dataToProcess->data, "multiple-choice")) {
           // $studyPrograms[] =
        }
        //todo
        if(property_exists( $dataToProcess->data, "single-choice")) {

        }
        //todo updateStudyProgram
        //
        //

        $stateData->enrolmentData = $updateEnrolment($stateValues, $stateValues->data->transactionId, $dataToProcess->data);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processIdendedDegreeProgram2), fn($stateValues) => $this->universityEntranceQualification($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }

    /**
     * @throws Exception
     */
    private function universityEntranceQualification(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->universityEntranceQualification);

        $stateData = $stateValues->data;
        $stateData->content = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->universityEntranceQualification), null];
    }

    /**
     * @throws Exception
     */
    public function processUniversityEntranceQualification(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processUniversityEntranceQualification);

        //validation is made by omnitracker backend
        $stateData = $stateValues->data;
        $stateData->enrolmentData = $updateEnrolment($stateValues, $stateValues->data->transactionId, $dataToProcess->data);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processUniversityEntranceQualification), fn($stateValues) => $this->portrait($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }

    /**
     * @throws Exception
     */
    private function portrait(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->portrait);

        $stateData = $stateValues->data;
        $stateData->content = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->portrait), null];
    }

    /**
     * @throws Exception
     */
    public function processPortrait(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processPortrait);
        //todo
        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processPortrait), fn($stateValues) => $this->previousStudies($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }

    /**
     * @throws Exception
     */
    private function previousStudies(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->previousStudies);

        $stateData = $stateValues->data;
        $stateData->content = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->previousStudies), null];
    }

    /**
     * @throws Exception
     */
    public function processPreviousStudies(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processPreviousStudies);
        //todo
        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processPreviousStudies), fn($stateValues) => $this->personalData($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }

    /**
     * @throws Exception
     */
    private function personalData(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->personalData);

        $stateData = $stateValues->data;
        $stateData->content = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->personalData), null];
    }

    /**
     * @throws Exception
     */
    public function processPersonalData(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processPersonalData);

        //validation is made by omnitracker backend
        $stateData = $stateValues->data;
        $stateData->enrolmentData = $updateEnrolment($stateValues, $stateValues->data->transactionId, $dataToProcess->data);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processPersonalData), fn($stateValues) => $this->legal($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }

    /**
     * @throws Exception
     */
    private function legal(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->legal);

        $stateData = $stateValues->data;
        $stateData->content = $this->readPageContent($stateValues, $stateValues->currentStateName, $objectFromJsonFile, $pageDataDirectory);
        $stateValues = FluxEcoStateMonad::setStateData($stateValues, $stateData);



        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->legal), null];
    }

    /**
     * @throws Exception
     */
    public function processLegal(FluxEcoStateValues|stdClass $stateValues, object $dataToProcess, callable $storeNewEnrolment, callable $updateEnrolment, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->processLegal);
        //todo
        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->processLegal), fn($stateValues) => $this->completed($stateValues, $objectFromJsonFile, $pageDataDirectory)];
    }

    /**
     * @throws Exception
     */
    private function completed(StateValues|stdClass $stateValues, callable $objectFromJsonFile, string $pageDataDirectory): array
    {
        $stateValues = FluxEcoStateMonad::changeCurrentStateName($stateValues, $this->stateNames->completed);

        //todo delete cookies, cached storage

        return [FluxEcoStateMonad::markStateAsCompleted($stateValues, $this->stateNames->completed), null];
    }


    private function initStateValues(): StateValues|stdClass
    {
        /**
         * @var StateValues|stdClass $stateValues
         */
        $stateValues = new stdClass();
        $stateValues->currentStateName = $this->stateNames->create;
        $stateValues->nextStateName = $this->stateNames->processCreate;
        $stateValues->finalStateName = $this->stateNames->completed;
        $stateValues->completedTransitionNames = [];
        $stateValues->uncompletedTransitionNames = [
            $this->stateNames->create,
            $this->stateNames->processCreate,
            $this->stateNames->identificationNumber,
            $this->stateNames->processIdentificationNumber,
            $this->stateNames->choiceSubject,
            $this->stateNames->processChoiceSubject,
            $this->stateNames->idendedDegreeProgram,
            $this->stateNames->processIdendedDegreeProgram,
            $this->stateNames->idendedDegreeProgram2,
            $this->stateNames->processIdendedDegreeProgram2,
            $this->stateNames->universityEntranceQualification,
            $this->stateNames->processUniversityEntranceQualification,
            $this->stateNames->portrait,
            $this->stateNames->processPortrait,
            $this->stateNames->previousStudies,
            $this->stateNames->processPreviousStudies,
            $this->stateNames->personalData,
            $this->stateNames->processPersonalData,
            $this->stateNames->legal,
            $this->stateNames->processLegal,
            $this->stateNames->completed,
        ];
        $stateValues->data = $this->init;

        return $stateValues;
    }

    /**
     *
     * helper
     */

    //todo - helper function or transition?
    //

    private function readPageContent(StateValues|stdClass $stateValues, string $pageName, callable $objectFromJsonFile, string $pageDataDirectory): object
    {
        $suffix = ".json";
        $fileName = $pageName . $suffix;
        return $this->resolveStateData($stateValues, $objectFromJsonFile($pageDataDirectory, $fileName));
    }

    /**
     * @throws Exception
     */
    private function resolveDynamicData(StateValues|stdClass $stateValues, object|array $data): object|array
    {
        if (is_object($data) === true) {
            $dataItems = get_object_vars($data);
        } else {
            $dataItems = $data;
        }

        $resolvedData = new stdClass();
        foreach ($dataItems as $key => $value) {
            if (is_object($value) === true) {
                if (property_exists($value, '$dynamic')) {
                    $method = $value->{'$dynamic'}->method;
                    $parameters = $this->resolveStateData($stateValues, $value->{'$dynamic'}->parameters);
                    $resolvedData->{"$key"} = $this->{$method}(...get_object_vars($parameters));
                    continue;
                }
                $resolvedData->{"$key"} = $this->resolveDynamicData($stateValues, $value);
                continue;
            }
            $resolvedData->{"$key"} = $value;
        }

        if (is_object($data) === true) {
            return $resolvedData;
        }
        return (array)$resolvedData;
    }

    private function readSubjectData(string $inputOptionDataDirectory,
                                     string $inputNameSubjects,
                                     int    $degreeProgram
    ): array
    {
        $suffix = ".json";
        $inputOptionFileName = $inputNameSubjects . $suffix;

        $objectFromJsonFile = $this->objectFromJsonFile;
        $data = (array)$objectFromJsonFile($inputOptionDataDirectory, $inputOptionFileName);

        return $data[$degreeProgram];
    }

    private function readChoosenSubjectData(int $subject, string $subjectTitle, int $subjectEcts): object
    {
        $object = new \stdClass();
        $object->id = $subject;
        $object->label = $subjectTitle;
        $object->ect = $subjectEcts;

        return $object;
    }

    private function readSubjectCombinationData(
        string $inputOptionDataDirectory,
        string $inputNameSubjectCombinations,
        int    $combination
    ): object
    {
        $suffix = ".json";
        $inputOptionFileName = $inputNameSubjectCombinations . $suffix;
        $objectFromJsonFile = $this->objectFromJsonFile;

        $choiceCombinations = (array)$objectFromJsonFile($inputOptionDataDirectory, $inputOptionFileName);
        return $choiceCombinations[$combination];
    }


    private function resolveStateData(StateValues|stdClass $stateValues, object|array $data): object|array
    {
        if (is_object($data) === true) {
            $dataItems = get_object_vars($data);
        } else {
            $dataItems = $data;
        }

        $resolvedStateData = new \stdClass();
        foreach ($dataItems as $key => $value) {
            if (is_object($value) === true) {
                if (property_exists($value, '$state')) {
                    $dottedKeyPath = $value->{'$state'};
                    $keyPathParts = explode(".", $dottedKeyPath);
                    $linkedValue = $stateValues;
                    foreach ($keyPathParts as $attributeKey) {
                        $linkedValue = $linkedValue->{$attributeKey};
                    }
                    $resolvedStateData->{"$key"} = $linkedValue;
                    continue;
                }
                $resolvedStateData->{"$key"} = $this->resolveStateData($stateValues, $value);
                continue;
            }
            $resolvedStateData->{"$key"} = $value;
        }

        if (is_object($data) === true) {
            return $resolvedStateData;
        }
        return (array)$resolvedStateData;
    }
}