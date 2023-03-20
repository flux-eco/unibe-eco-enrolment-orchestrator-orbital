<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Omnitracker;


use InvalidArgumentException;

enum ActionName: string
{
    case GET_CERTIFICATES = "GetListStudienberechtigungsausweis";
    case GET_CERTIFICATES_TYPES = "GetListStudienberechtigungsausweistyp";
}