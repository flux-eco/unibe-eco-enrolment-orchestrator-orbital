<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Inputs;

use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\{Schemas, Configs, Data};
use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\InputsAdapter;
use Exception;
use JsonSerializable;

final class InputsProvider
{
    private ?Schemas\InputSchemas $inputSchemas = null;
    /**
     * @var InputsAdapter\OptionList[] array
     */
    private array $optionLists = [];
    private array $optionListsDependencies = [];
    private string $inputSchemasJsonFileName;
    private string $optionListsJsonFileName;
    private string $optionListsDependenciesJsonFileName;
    private string $optionListsDependenciesInitialIndexJsonFileName;

    private function __construct(
        private readonly Configs\Outbounds $outbounds,
        private readonly string            $jsonFilesDirectoryPath
    )
    {
        $this->inputSchemasJsonFileName = "university-entrance-qualification-input-schemas.json";
        $this->optionListsJsonFileName = "university-entrance-qualification-option-lists.json";
        $this->optionListsDependenciesJsonFileName = "university-entrance-qualification-option-lists-dependencies.json";
        $this->optionListsDependenciesInitialIndexJsonFileName = "university-entrance-qualification-option-lists-dependencies-initial-list-index.json";
    }

    public static function new(Configs\Outbounds $outbounds, string $jsonFilesDirectoryPath): self
    {
        return new self($outbounds, $jsonFilesDirectoryPath);
    }

    public function writeJsonFiles(
        Schemas\InputSchemas $inputSchemas,
        Data\RawData         $rawData,
    ): void
    {
        $this->writeJsonData($this->inputSchemasJsonFileName, $inputSchemas);
        $this->inputSchemas = $inputSchemas;

        $this->writeOptionLists($inputSchemas, $rawData);
        $this->writeOptionListsDependencies($inputSchemas, $rawData);
    }

    private function writeOptionLists(Schemas\InputSchemas $inputSchemas,
                                      Data\RawData         $rawData): void
    {
        $optionLists = [
            ...InputsAdapter\OptionList::newWithOptionItemTransformation($inputSchemas->certificateTypes->optionItemListIndex, $rawData->certificateTypes, fn($object) => InputsAdapter\Option::fromDataObject($object, "id", "label"))->jsonSerialize(),
            ...InputsAdapter\OptionList::newWithOptionItemTransformation($inputSchemas->certificatesIssueYears->optionItemListIndex, $rawData->certificateIssueYears, fn($object) => InputsAdapter\Option::fromDataObject($object, "id"))->jsonSerialize(),
            ...InputsAdapter\OptionList::newWithOptionItemTransformation($inputSchemas->certificates->optionItemListIndex, $rawData->certificates, fn($object) => InputsAdapter\Option::fromDataObject($object, "id", "label"))->jsonSerialize(),
            ...InputsAdapter\OptionList::newWithOptionItemTransformation($inputSchemas->maturaCanton->optionItemListIndex, $rawData->cantons, fn($object) => InputsAdapter\Option::fromDataObject($object, "id", "label"))->jsonSerialize(),
            ...InputsAdapter\OptionList::newWithOptionItemTransformation($inputSchemas->upperSecondarySchool->optionItemListIndex, $rawData->schools, fn($object) => InputsAdapter\Option::fromDataObject($object, "id", "label"))->jsonSerialize(),
            ...InputsAdapter\OptionList::newWithOptionItemTransformation($inputSchemas->certificateCountries->optionItemListIndex, $rawData->countries, fn($object) => InputsAdapter\Option::fromDataObject($object, "id", "label"))->jsonSerialize(),
            ...InputsAdapter\OptionList::newWithOptionItemTransformation($inputSchemas->certificateCanton->optionItemListIndex, $rawData->cantons, fn($object) => InputsAdapter\Option::fromDataObject($object, "id", "label"))->jsonSerialize(),
            ...InputsAdapter\OptionList::newWithOptionItemTransformation($inputSchemas->municipalities->optionItemListIndex, $rawData->municipalities, fn($object) => InputsAdapter\Option::fromDataObject($object, "id", "label"))->jsonSerialize(),
        ];
        $this->writeJsonData($this->optionListsJsonFileName, $optionLists);
        $this->optionLists = $optionLists;
    }

    private function writeOptionListsDependencies(
        Schemas\InputSchemas $inputSchemas,
        Data\RawData         $rawData
    ): void
    {
        //todo
        $select_index = $this->loadOptionListDependency(
            $inputSchemas->certificateTypes->inputName,
            array_map(fn(array $certificate_type): array => [$certificate_type["id"],
                $this->loadOptionListDependency(
                    $inputSchemas->certificatesIssueYears->inputName,
                    array_map(fn(string $issue_year): array => [$issue_year,
                        $this->loadOptionListDependency(
                            $inputSchemas->certificates->inputName,
                            array_map(fn(array $raw_certificate): array => [$raw_certificate["id"],
                                $this->loadOptionListDependency(
                                    $inputSchemas->maturaCanton->inputName,
                                    array_map(fn(array $canton): array => [$canton["id"],
                                        $this->loadOptionListDependency(
                                            $inputSchemas->maturaCanton->inputName,
                                            array_map(fn(array $school): array => [$school["id"],
                                                $this->getCountryDependentSelectIndex($rawData, $inputSchemas)
                                            ], array_values(array_filter($rawData->schools, fn(array $school): bool => $rawData->schoolCanton[$school['id']] === $canton['id'] && in_array($raw_certificate['id'], $rawData->schoolCertificates[$school['id']])))))], array_values($rawData->cantons))
                                )], array_values(array_filter($rawData->certificates, fn(array $raw_certificate): bool => $raw_certificate["certificateTypeId"] === $certificate_type["id"] && array_search($issue_year,
                                    $this->extractIssueYearsFromCertificates([$rawData->certificates]), true) !== false)))
                        )], $this->extractIssueYearsFromCertificates(array_values(array_filter($rawData->certificates, fn(array $raw_certificate): bool => $raw_certificate["certificateTypeId"] === $certificate_type["id"]))))
                )], array_values($rawData->certificateTypes))
        );
        $this->writeJsonData($this->optionListsDependenciesJsonFileName, $this->optionListsDependencies);
        $this->writeJsonData($this->optionListsDependenciesInitialIndexJsonFileName, [$select_index]);
    }

    function extractIssueYearsFromCertificates(array $raw_certificates) : array {
        $issue_years = [];
        foreach ($raw_certificates as $raw_certificate) {
            for ($issue_year = $raw_certificate["minIssueYear"]; $issue_year <= $raw_certificate["maxIssueYear"]; $issue_year++) {
                $issue_years[] = strval($issue_year);
            }
        }
        sort($issue_years);
        return array_values(array_unique($issue_years));
    }


    private function loadOptionListDependency(string $select_type, array $select_options): int
    {
        $select = [array_key_first(array_filter($this->optionListsDependencies, fn(array $_data): bool => $_data[0] === $select_type)), $select_options];
        $select_index = array_search($select, $this->optionLists, true);
        if ($select_index !== false) {
            return $select_index;
        }
        return array_push($this->optionLists, $select) - 1;
    }


    private function getCountryDependentSelectIndex(Data\RawData $rawData, Schemas\InputSchemas $inputSchemas): int
    {
        return $this->loadOptionListDependency(
            $inputSchemas->certificateCountries->inputName,
            array_map(fn(array $country): array => [
                $country->id,
                $this->getCantonsDependentSelectIndex($rawData, $inputSchemas, $country)
            ], array_values($rawData->countries))
        );
    }

    private function getCantonsDependentSelectIndex(Data\RawData         $rawData,
                                                    Schemas\InputSchemas $inputSchemas,
                                                    InputsAdapter\Option $country): ?int
    {
        if ($country["id"] === $rawData->countrySwitzerlandUniqueId) {
            return $this->loadOptionListDependency(
                $inputSchemas->certificateCanton->inputName,
                array_map(fn(array $canton): array => [
                    $canton["id"],
                    $this->getCantonMuncipalitiesDependendSelectIndex($rawData, $inputSchemas, $canton)
                ], array_values($rawData->cantons)
                ));
        }
        return null;
    }

    private function getCantonMuncipalitiesDependendSelectIndex(Data\RawData         $rawData,
                                                                Schemas\InputSchemas $inputSchemas,
                                                                InputsAdapter\Option $canton): ?int
    {
        if (array_key_exists($canton["id"], $rawData->cantonMuncipalities) === false) {
            return null;
        }
        return $this->loadOptionListDependency(
            $inputSchemas->municipalities->inputName,
            array_values($rawData->cantonMuncipalities[$canton["id"]])
        );
    }

    /**
     * @throws Exception
     */
    private function writeJsonData(string $jsonFileName, JsonSerializable|array $data): void
    {
        $filePath = $this->getAbsoluteFilePath($jsonFileName);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        file_put_contents($this->getAbsoluteFilePath($jsonFileName), json_encode($data));
    }

    /**
     * @throws Exception
     */
    private function getAbsoluteFilePath(string $jsonFileName): string
    {
        return $this->outbounds->jsonFileReader->getAbsoluteFilePath($this->jsonFilesDirectoryPath, $jsonFileName);
    }
}