<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports;

use UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Repositories\EnrolmentConfigurationReferenceObjectRepository;

final readonly class  Outbounds {

    private function __construct(
        public string $configFilesDirectoryPath,
        public EnrolmentConfigurationReferenceObjectRepository $enrolmentConfigurationReferenceObjectsRepository,
        public Dispatchers\ConfigurationMessageDispatcher $configurationMessageDispatcher,
        public Dispatchers\EnrolmentMessageDispatcher $enrolmentMessageDispatcher,
        public Repositories\EnrolmentRepository $enrolmentRepository
    )
    {

    }

    public static function new(
        string $configFilesDirectoryPath,
        EnrolmentConfigurationReferenceObjectRepository $enrolmentConfigurationReferenceObjectsRepository,
        Dispatchers\ConfigurationMessageDispatcher $configurationMessageDispatcher,
        Dispatchers\EnrolmentMessageDispatcher $enrolmentMessageDispatcher,
        Repositories\EnrolmentRepository $enrolmentRepository
    ): self {
        return new self(...get_defined_vars());
    }

}