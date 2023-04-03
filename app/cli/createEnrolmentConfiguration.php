<?php
ini_set('memory_limit', '-1');
require_once __DIR__."/../autoload.php";

$cliApi = UnibeEco\EnrolmentOrchestratorOrbital\CliApi::new();
$cliApi->writeJsonFiles();