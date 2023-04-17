<?php
require_once __DIR__ . "/flux-eco/php/autoload.php";
require_once __DIR__ . "/flux-eco-json-file-processor/autoload.php";
require_once __DIR__ . "/flux-eco-unibe-omnitracker-client/autoload.php";
require_once __DIR__ . "/flux-eco-object-mapper/autoload.php";
require_once __DIR__ . "/flux-eco-http-workflow-request-handler/autoload.php";
require_once __DIR__ . "/flux-eco-unibe-enrolment/autoload.php";

spl_autoload_register(function (string $class) {
    $namespace = "libphonenumber";
    $baseDirectory = __DIR__ . '/libphonenumber/src';
    loadPhonenumberClassFiles($namespace, $class, $baseDirectory);
});

/**
 * @param string $namespace
 * @param string $class
 * @param string $baseDirectory
 * @return void
 */
function loadPhonenumberClassFiles(string $namespace, string $class, string $baseDirectory): void
{
    $classNameParts = explode($namespace, $class);
    // not our responsibility
    if (count($classNameParts) !== 2) {
        return;
    }
    $filePath = str_replace('\\', '/', $classNameParts[1]) . '.php';
    require $baseDirectory . $filePath;
}