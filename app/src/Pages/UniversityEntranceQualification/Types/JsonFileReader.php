<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\UniversityEntranceQualification\Types;

interface JsonFileReader
{
    public function getAbsoluteFilePath(string $directoryPath, string $jsonFileName): string;

    public function readJsonFile(string $absoluteJsonFilePath): array|object;
}