<?php

spl_autoload_register(function (string $class) {
    $namespace = "FluxEco\\HttpWorkflowRequestHandler";
    $baseDirectory = __DIR__.'/src';
    loadHttpWorkflowRequestHandlerClassFiles($namespace, $class, $baseDirectory);
});

/**
 * @param string $namespace
 * @param string $class
 * @param string $baseDirectory
 * @return void
 */
function loadHttpWorkflowRequestHandlerClassFiles(string $namespace, string $class, string $baseDirectory): void
{
    $classNameParts = explode($namespace, $class);
    // not our responsibility
    if (count($classNameParts) !== 2) {
        return;
    }
    $filePath = str_replace('\\', '/', $classNameParts[1]) . '.php';
    require $baseDirectory . $filePath;
}