<?php

namespace FluxEco\JsonFileProcessor;

use Exception;
use JsonSerializable;

//todo
//schema resolveRef
//states resolveLink $link

class Api
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
     * @throws Exception
     */
    public static function new(
        array $allowedUsers = [1000]
    ): self
    {
        if (empty($allowedUsers)) {
            throw new Exception("You must declare at least one allowed user for reading JSON files.");
        }

        return new self($allowedUsers);
    }

    /**
     * @throws Exception
     */
    public function getAbsoluteFilePath(string $directoryPath, string $jsonFileName): string
    {
        if (!preg_match('/^[a-zA-Z0-9-_\.]+$/', $jsonFileName)) {
            throw new Exception("Invalid filename: " . $jsonFileName);
        }

        $absoluteFilePath = realpath(implode("/", [$directoryPath, $jsonFileName]));


        if (str_contains($absoluteFilePath, "//") !== false
            || str_contains($absoluteFilePath, "\\") !== false
        ) {
            throw new Exception("file path not valid " . $absoluteFilePath);
        }

        //todo str_contains($absoluteFilePath, "..") -> check if file-path is within allowed path

        if (!file_exists($absoluteFilePath)) {
            throw new Exception("File not found - absoluteFilePath: " . $absoluteFilePath . " - directoryPath: " . $directoryPath . " - jsonFileName: " . $jsonFileName);
        }

        if (!is_readable($absoluteFilePath)) {
            throw new Exception("File is not readable: " . $absoluteFilePath);
        }

        if (!in_array(fileowner($absoluteFilePath), $this->allowedUsers)) {
            throw new Exception("Access denied: " . $absoluteFilePath . "file owner " . fileowner($absoluteFilePath) . " allowed users " . print_r($this->allowedUsers, true));
        }

        return $absoluteFilePath;
    }

    /**
     * @throws Exception
     */
    public function readJsonFile(string $directoryPath, string $jsonFileName): object|array
    {
        $absoluteJsonFilePath = $this->getAbsoluteFilePath($directoryPath, $jsonFileName);

        $json = file_get_contents($absoluteJsonFilePath);
        $decodedJson = json_decode($json);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON: " . json_last_error_msg());
        }
        $resolvedRefs = $this->resolveRefs($decodedJson, $absoluteJsonFilePath);

        return $resolvedRefs;
    }

    /**
     * @throws Exception
     */
    public function readJsonSchemeDefaults(string $directoryPath, string $jsonFileName): object
    {
        $jsonObject = $this->readJsonFile($directoryPath, $jsonFileName);
        return $this->readDefaults($jsonObject);
    }

    /**
     * @throws Exception
     */
    private function readDefaults(object|array $data): object|array
    {
        if (is_object($data) === true) {
            $dataItems = get_object_vars($data->properties);
        } else {
            $dataItems = $data;
        }

        $resolvedDefaultData = new \stdClass();
        foreach ($dataItems as $key => $value) {
            if (is_object($value) === true) {
                if (property_exists($value, 'default')) {
                    $resolvedDefaultData->{"$key"} = $value->{'default'};
                    continue;
                }
            }
        }


        if (is_object($data) === true) {
            return $resolvedDefaultData;
        }
        return (array)$resolvedDefaultData;
    }

    /**
     * @throws Exception
     */
    private function resolveRefs(object|array $data, string $currentFilePath): object|array
    {
        if (is_object($data) === true) {
            $dataItems = get_object_vars($data);
        } else {
            $dataItems = $data;
        }

        $resolvedRefsData = new \stdClass();
        foreach ($dataItems as $key => $value) {
            if (is_object($value) === true) {
                if (property_exists($value, '$ref')) {
                    //todo the other possibilities
                    $refPath = $value->{'$ref'};
                    $refFileName = pathinfo($refPath, PATHINFO_BASENAME);
                    $dirName = realpath(implode("/", [pathinfo($currentFilePath, PATHINFO_DIRNAME), pathinfo($refPath, PATHINFO_DIRNAME)]));
                    $resolvedRefsData->{"$key"} = $this->readJsonFile($dirName, $refFileName);
                    continue;
                }
                $resolvedRefsData->{"$key"} = $this->resolveRefs($value, $currentFilePath);
                continue;
            }
            $resolvedRefsData->{"$key"} = $value;
        }


        if (is_object($data) === true) {
            return $resolvedRefsData;
        }
        return (array)$resolvedRefsData;
    }


    public function writeJsonFile($absoluteFilePath, object|array $data): void
    {
        if (str_contains($absoluteFilePath, "..")
            || str_contains($absoluteFilePath, "//") !== false
            || str_contains($absoluteFilePath, "\\") !== false
        ) {
            throw new Exception("file path not valid " . $absoluteFilePath);
        }

        if (file_exists($absoluteFilePath)) {
            unlink($absoluteFilePath);
        }

        file_put_contents($absoluteFilePath, json_encode($data));
    }
}