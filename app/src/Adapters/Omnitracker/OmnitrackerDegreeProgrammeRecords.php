<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Omnitracker;


use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config\OmnitrackerBinding;


use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities\Certificate;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Entities\CertificateType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\LanguageCode;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\Enums\ObjectType;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\CertificateIssueYear;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\CertificateTypeIssueYearRange;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\SelectInputOption;
use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects\Year;
use WeakMap;

final class OmnitrackerDegreeProgrammeRecords
{
    private string $wsdlFilePath = 'Studis/Studiengang.svc?wsdl';

    private WeakMap $subjects;
    private WeakMap $certificates;
    private WeakMap $certificateTypes;

    private int $currentDependentSelectIndex = 0;

    private ?ValueObjects\DependentInputSelect $dependentCertificateTypesSelect;
    private WeakMap $dependentIssueYearsSelect;
    private WeakMap $dependentCertificatesSelect;
    private WeakMap $dependentCertificateTypesInputOptions;
    private WeakMap $dependentCertificatesYearsInputOptions;
    private WeakMap $dependentCertificatesInputOptions;


    private function __construct(
        private OmnitrackerBinding $binding,
        private string             $configFilesDirectoryPath,
    )
    {
        $this->issueYears = new WeakMap();
        $this->certificates = new WeakMap();
        $this->certificateTypes = new WeakMap();
        $this->dependentCertificateTypesSelect = null;
        $this->dependentIssueYearsSelect = new WeakMap();
        $this->dependentCertificatesSelect = new WeakMap();
        $this->dependentCertificateTypesInputOptions = new WeakMap();
        $this->dependentCertificatesYearsInputOptions = new WeakMap();
        $this->dependentCertificatesInputOptions = new WeakMap();

        $this->getCertificateTypesDependentSelect();
    }

    public static function new(
        OmnitrackerBinding $binding,
        string             $configFilesDirectoryPath
    ): OmnitrackerHelpTableRecords
    {
        return new self($binding, $configFilesDirectoryPath);
    }

    public function getUniversityQualificationDependentSelects(): array
    {
        $dependentSelects[] = $this->loadCertificateTypesDependentSelect();
        foreach ($this->dependentIssueYearsSelect->getIterator() as $dependentSelect) {
            $dependentSelects[] = $dependentSelect;
        }
        foreach ($this->dependentCertificatesSelect->getIterator() as $dependentSelect) {
            $dependentSelects[] = $dependentSelect;
        }
        return $dependentSelects;
    }

    public function getCertificateTypesDependentSelect(): ValueObjects\DependentInputSelect
    {
        return $this->dependentCertificateTypesSelect ??= $this->loadCertificateTypesDependentSelect();
    }

    /**
     * @return SelectInputOption[] array
     */
    public function getCertificateTypeSelectInputOptions(): array
    {
        echo "getCertificateTypeSelectInputOptions" . PHP_EOL;
        return $this->getSelectInputOptions($this->dependentCertificateTypesInputOptions, $this->getCertificateTypes(LanguageCode::DE));
    }

    /**
     * @return SelectInputOption[] array
     */
    public function getIssueYearSelectInputOptions(): array
    {
        echo "getIssueYearSelectInputOptions" . PHP_EOL;
        return $this->getSelectInputOptions($this->dependentCertificatesYearsInputOptions, $this->getIssueYears());
    }

    /**
     * @return SelectInputOption[] array
     */
    public function getCertificateSelectInputOptions(): array
    {
        echo "getCertificateSelectInputOptions" . PHP_EOL;
        return $this->getSelectInputOptions($this->dependentCertificatesInputOptions, $this->getCertificates(LanguageCode::DE));
    }

    /**
     * @return SelectInputOption[] array
     */
    private function getSelectInputOptions(WeakMap $weakMap, array $objects): array
    {
        echo "getSelectInputOptions" . PHP_EOL;
        $inputOptions = [];
        //todo
        foreach ($weakMap->getIterator() as $dependentSelectInputOptions) {
            foreach ($dependentSelectInputOptions as $dependentInputOption) {
                if (key_exists($dependentInputOption->choiceIndex, $objects) === false) {
                    continue;
                }

                /**
                 * @var ValueObjects\DependentSelectInputOption $dependentInputOption
                 */
                $inputOptions[$dependentInputOption->choiceIndex] = ValueObjects\SelectInputOption::new(
                    $dependentInputOption->choiceIndex,
                    $objects[$dependentInputOption->choiceIndex]->label
                );
            }
        }
        return $inputOptions;
    }

    private function loadCertificateTypesDependentSelect(): ValueObjects\DependentInputSelect
    {
        echo "getCertificateTypesDependentSelect" . PHP_EOL;
        return ValueObjects\DependentInputSelect::new(
            $this->currentDependentSelectIndex,
            ValueObjects\InputType::CERTIFICATE_TYPE->value,
            $this->getCertificateTypesDependentSelectInputOptions()
        );
    }

    private function getCertificateTypesDependentSelectInputOptions(): array
    {
        echo "getCertificateTypesDependentSelectInputOptions" . PHP_EOL;
        return $this->dependentCertificateTypesInputOptions[ObjectType::CERTIFICATE_TYPE] ??= $this->loadCertificateTypesDependentSelectInputOptions();
    }

    private function loadCertificateTypesDependentSelectInputOptions(): array
    {
        echo "loadCertificateTypesDependentSelectInputOptions" . PHP_EOL;
        $selectInputOptions = [];
        foreach ($this->getCertificateTypesIssueYearRanges() as $certificateTypeIssueYearRange) {
            $selectInputOptions[$certificateTypeIssueYearRange->certificateTypeId] = ValueObjects\DependentSelectInputOption::new(
                $certificateTypeIssueYearRange->certificateTypeId,
                $this->getDependentSelectIndexForCertificateTypeIssueYearRange($certificateTypeIssueYearRange)
            );
        }
        return $selectInputOptions;
    }


    private function getDependentSelectIndexForCertificateTypeIssueYearRange(CertificateTypeIssueYearRange $certificateTypeIssueYearRange): int
    {
        echo "getDependentSelectIndexForCertificateTypeIssueYearRange" . PHP_EOL;
        return $this->getIssueYearDependentSelect($certificateTypeIssueYearRange)->selectIndex;
    }

    private function getIssueYearDependentSelect(CertificateTypeIssueYearRange $certificateTypeIssueYearRange): ValueObjects\DependentInputSelect
    {
        echo "getIssueYearDependentSelect" . PHP_EOL;
        return $this->dependentIssueYearsSelect[$certificateTypeIssueYearRange] ??= $this->loadIssueYearSelect($certificateTypeIssueYearRange);
    }

    private function loadIssueYearSelect(CertificateTypeIssueYearRange $certificateTypeIssueYearRange): ValueObjects\DependentInputSelect
    {
        echo "loadIssueYearSelect" . PHP_EOL;
        $this->currentDependentSelectIndex += 1;
        return ValueObjects\DependentInputSelect::new(
            $this->currentDependentSelectIndex,
            ValueObjects\InputType::ISSUE_YEAR->value,
            $this->getCertificateTypeIssueYearDependentSelectInputOptions($certificateTypeIssueYearRange)
        );
    }

    private function getCertificateTypeIssueYearDependentSelectInputOptions(CertificateTypeIssueYearRange $certificateTypeIssueYearRange): array
    {
        echo "getCertificateTypeIssueYearDependentSelectInputOptions" . PHP_EOL;
        return $this->dependentCertificatesYearsInputOptions[$certificateTypeIssueYearRange] ??= $this->loadCertificateTypeIssueYearSelectInputOptions($certificateTypeIssueYearRange);
    }

    private function loadCertificateTypeIssueYearSelectInputOptions(CertificateTypeIssueYearRange $certificateTypeIssueYearRange): array
    {
        echo "loadCertificateTypeIssueYearSelectInputOptions" . PHP_EOL;
        $selectInputOptions = [];

        $minYear = $certificateTypeIssueYearRange->minYear;
        $maxYear = $certificateTypeIssueYearRange->maxYear;

        for ($year = $minYear; $year <= $maxYear; $year++) {
            $selectInputOptions[$year] = ValueObjects\DependentSelectInputOption::new(
                $year,
                $this->getDependentSelectIndexForCertificateTypeYearRange($certificateTypeIssueYearRange)
            );

        }

        return $selectInputOptions;
    }

    public function getDependentSelectIndexForCertificateTypeYearRange(CertificateTypeIssueYearRange $certificateTypeIssueYearRange): int
    {
        echo "getDependentSelectIndexForCertificateTypeYearRange" . PHP_EOL;
        return $this->getCertificateDependentSelect($certificateTypeIssueYearRange)->selectIndex;
    }

    private function getCertificateDependentSelect(CertificateTypeIssueYearRange $certificateTypeIssueYearRange): ValueObjects\DependentInputSelect
    {
        echo "getCertificateDependentSelect" . PHP_EOL;
        return $this->dependentCertificatesSelect[$certificateTypeIssueYearRange] ??= $this->loadCertificateDependentSelect($certificateTypeIssueYearRange);
    }

    private function loadCertificateDependentSelect(CertificateTypeIssueYearRange $certificateTypeIssueYearRange): ValueObjects\DependentInputSelect
    {
        echo "loadCertificateDependentSelect" . PHP_EOL;
        $this->currentDependentSelectIndex += 1;
        return ValueObjects\DependentInputSelect::new(
            $this->currentDependentSelectIndex,
            ValueObjects\InputType::CERTIFICATE->value,
            $this->getCertificateDependentSelectInputOptions($certificateTypeIssueYearRange)
        );
    }


    private function getCertificateDependentSelectInputOptions($certificateTypeIssueYearRange): array
    {
        echo "getCertificateDependentSelectInputOptions" . PHP_EOL;
        return $this->dependentCertificatesInputOptions[$certificateTypeIssueYearRange] ??= $this->loadCertificateSelectInputOptions($certificateTypeIssueYearRange);
    }

    private function loadCertificateSelectInputOptions(CertificateTypeIssueYearRange $certificateTypeIssueYearRange): array
    {
        echo "loadCertificateSelectInputOptions" . PHP_EOL;
        $certificates = $this->getCertificates(LanguageCode::DE);
        $selectInputOptions = [];

        foreach ($certificates as $certificate) {
            if ($certificate->certificateTypeId === $certificateTypeIssueYearRange->certificateTypeId
                &&
                (
                    $certificate->minIssueYear >= $certificateTypeIssueYearRange->minYear
                    || $certificate->maxIssueYear <= $certificateTypeIssueYearRange->maxYear
                )
            ) {
                $selectInputOptions[$certificate->id] = ValueObjects\DependentSelectInputOption::new(
                    $certificate->id,
                    1
                );
            }
        }

        return $selectInputOptions;
    }

    public function writeCertificateTypesChoiceOptions(): void
    {
        $certificateTypes = $this->getCertificateTypes(LanguageCode::DE);

        $choiceOptions = [];
        foreach ($certificateTypes as $certificateType) {
            $choiceOptions[$certificateType->id] = ValueObjects\SelectInputOption::new(
                $certificateType->id,
                $certificateType->label->localizedValues
            );
        }

        $this->writeReferenceObject(ObjectType::CERTIFICATE_TYPES, $choiceOptions);
    }

    /**
     * @param LanguageCode $languageCode
     * @return Certificate[]
     */
    public function getCertificates(LanguageCode $languageCode): array
    {
        $actionParameters = $this->getDefaultActionParameters($languageCode);
        $querySettings = QuerySettings::new(
            ActionName::GET_CERTIFICATES->value,
            $actionParameters
        );
        return $this->certificates[$querySettings] ??= $this->loadCertificates($querySettings);
    }

    /**
     * @param QuerySettings $querySettings
     * @return Certificate[]
     */
    private function loadCertificates(QuerySettings $querySettings): array
    {
        $collection = json_decode($this->readCollection($querySettings)->{ObjectName::GET_CERTIFICATES_RESULTS->value});

        $resultList = [];
        foreach ($collection as $item) {
            if ($this->hasCertificateType($item->TypUniqueId) === false) {
                continue;
            }

            $resultList[$item->UniqueId] = Certificate::new(
                $item->UniqueId,
                [ValueObjects\LocalizedStringValue::new(
                    $querySettings->actionParameters->{ActionParameterName::LANGUAGE_CODE->value},
                    $item->Title
                )],
                $item->GueltigAb,
                $item->GueltigBis,
                $item->TypUniqueId
            );
        }
        return $resultList;
    }

    public function hasCertificateType(int $certificateTypeId): bool
    {
        $certificateTypes = $this->getCertificateTypes(LanguageCode::DE);
        return key_exists($certificateTypeId, $certificateTypes);
    }

    public function getCertificateType(int $certificateTypeId): CertificateType
    {
        $certificateTypes = $this->getCertificateTypes(LanguageCode::DE);
        return $certificateTypes[$certificateTypeId];
    }


    /**
     * @param LanguageCode $languageCode
     * @return CertificateType[]
     */
    public function getCertificateTypes(LanguageCode $languageCode): array
    {
        $actionParameters = $this->getDefaultActionParameters($languageCode);
        $actionParameters->pType = 1;

        $querySettings = QuerySettings::new(
            ActionName::GET_CERTIFICATES_TYPES->value,
            $actionParameters
        );

        return $this->certificateTypes[$querySettings] ??= $this->loadCertificateTypes($querySettings);
    }


    private function loadCertificateTypes(QuerySettings $querySettings): array|object
    {
        $collection = json_decode($this->readCollection($querySettings)->{ObjectName::GET_CERTIFICATES_TYPES_RESULTS->value});

        $resultList = [];
        foreach ($collection as $item) {
            $resultList[$item->UniqueId] = CertificateType::new(
                $item->UniqueId,
                [ValueObjects\LocalizedStringValue::new(
                    $querySettings->actionParameters->{ActionParameterName::LANGUAGE_CODE->value},
                    $item->Title
                )],
                $item->WohngemeindeErforderlich
            );
        }
        return $resultList;
    }

    /**
     * @return CertificateTypeIssueYearRange[]
     */
    public function getCertificateTypesIssueYearRanges(): array
    {
        $certificates = $this->getCertificates(LanguageCode::DE);
        foreach ($certificates as $certificate) {
            $this->getCertificateIssueYears($certificate);
        }
        $iterator = $this->issueYears->getIterator();

        $ranges = [];
        foreach ($iterator as $key => $value) {
            $ranges[] = $key;
        }
        return $ranges;
    }

    public function getCertificateIssueYears(Certificate $certificate): array
    {
        if ($certificate->certificateTypeId === 0) {
            return [];
        }
        $certificateTypeYearRange = CertificateTypeIssueYearRange::new(
            $certificate->certificateTypeId, $certificate->minIssueYear, $certificate->maxIssueYear
        );
        return $this->issueYears[$certificateTypeYearRange] ??= $this->loadCertificateIssueYears($certificateTypeYearRange);
    }

    /**
     * @param CertificateTypeIssueYearRange $yearRange
     * @return CertificateIssueYear[]
     */
    private function loadCertificateIssueYears(CertificateTypeIssueYearRange $yearRange): array
    {
        $minYear = $yearRange->minYear;
        $maxYear = $yearRange->maxYear;

        $issueYears = [];
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $issueYears[] = CertificateIssueYear::new($year);
        }

        return $issueYears;
    }

    /**
     * @return Year[]
     */
    private function getIssueYears(): array
    {
        $minYear = date('Y') - 60;
        $maxYear = date('Y');
        $issueYears = [];
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $issueYears[$year] = Year::new((string)$year);
        }
        return $issueYears;
    }

    private function readCollection(QuerySettings $querySettings): object|array
    {
        $client = $this->newSoapClient();
        return $client->{$querySettings->actionName}($querySettings->actionParameters);
    }

    private function newSoapClient(): \SoapClient
    {
        try {
            return new \SoapClient($this->binding->omnitrackerSoapServer->toString() . "/" . $this->wsdlFilePath,
                ['connection_timeout' => '1']);
        } catch (SOAPFault $e) {
            print_r($e->faultstring);
        }
    }

    private function getDefaultActionParameters(LanguageCode $languageCode): object
    {
        $actionParameters = new \stdClass();
        $actionParameters->{ActionParameterName::OT_PASSWORD->value} = $this->binding->omnitrackerCredentials->password;
        $actionParameters->{ActionParameterName::OT_SERVER->value} = $this->binding->omnitrackerServer;
        $actionParameters->{ActionParameterName::OT_USER->value} = $this->binding->omnitrackerCredentials->user;
        $actionParameters->{ActionParameterName::LANGUAGE_CODE->value} = $languageCode->value;

        return $actionParameters;
    }

    public function writeReferenceObject(string $name, array|object $referenceObject): void
    {
        $filePath = $this->configFilesDirectoryPath . "/reference-objects/" . $name . ".json";
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $jsonDocument = fopen($filePath, "w");
        fwrite($jsonDocument, json_encode($referenceObject));
    }
}