<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\QueryAdapter;

use UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification;

final readonly class JsonFileReaderAdapter implements UniversityEntranceQualification\Configs\JsonFileReader
{
    /**
     * @param int[] $allowedUsers
     */
    private function __construct(
        public array $allowedUsers
    )
    {

    }

    /**
     * @param int[] $allowedUsers
     * @return self
     * @throws \Exception
     */
    public static function new(
        array $allowedUsers = [1000]
    ): self
    {
        if (empty($allowedUsers)) {
            throw new \Exception("You must declare at least one allowed user for reading JSON files.");
        }

        return new self($allowedUsers);
    }

    /**
     * @throws \Exception
     */
    public function getAbsoluteFilePath(string $directoryPath, string $jsonFileName): string
    {
        if (!preg_match('/^[a-zA-Z0-9-_\.]+$/', $jsonFileName)) {
            throw new \Exception("Invalid filename: " . $jsonFileName);
        }

        $absoluteFilePath = realpath(implode("/", [$directoryPath, $jsonFileName]));


        if (str_contains($absoluteFilePath, "..")
            || str_contains($absoluteFilePath, "//") !== false
            || str_contains($absoluteFilePath, "\\") !== false
        ) {
            throw new \Exception("file path not valid " . $absoluteFilePath);
        }

        if (!file_exists($absoluteFilePath)) {
            throw new \Exception("File not found: " . $absoluteFilePath);
        }

        if (!is_readable($absoluteFilePath)) {
            throw new \Exception("File is not readable: " . $absoluteFilePath);
        }

        if (!in_array(fileowner($absoluteFilePath), $this->allowedUsers)) {
            throw new \Exception("Access denied: " . $absoluteFilePath);
        }

        return $absoluteFilePath;
    }

    /**
     * @throws \Exception
     */
    public function readJsonFile(string $absoluteJsonFilePath): array|object
    {
        $filePath = $this->getAbsoluteFilePath(pathinfo($absoluteJsonFilePath, PATHINFO_DIRNAME), pathinfo($absoluteJsonFilePath, PATHINFO_BASENAME));

        $json = file_get_contents($filePath);
        $decodedJson = json_decode($json);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON: " . json_last_error_msg());
        }

        if (isset($decodedJson->scripts) || isset($decodedJson->links)) {
            throw new \Exception("JSON contains potentially harmful content");
        }

        return $decodedJson;
    }
}
