<?php

namespace  UnibeEco\EnrolmentOrchestratorOrbital\Core\Ports;

final readonly class  Outbounds {

    private function __construct(
        public Dispatchers\ConfigurationMessageDispatcher $configurationMessageDispatcher,
        public Dispatchers\EnrolmentMessageDispatcher $enrolmentMessageDispatcher,
        public Repositories\EnrolmentRepository $enrolmentRepository
    )
    {

    }

    public static function new(
        Dispatchers\ConfigurationMessageDispatcher $configurationMessageDispatcher,
        Dispatchers\EnrolmentMessageDispatcher $enrolmentMessageDispatcher,
        Repositories\EnrolmentRepository $enrolmentRepository
    ): self {
        return new self(...get_defined_vars());
    }

}