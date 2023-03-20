<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Omnitracker;

use InvalidArgumentException;

enum ActionParameterName: string
{
    case OT_SERVER = "pOTServer";
    case OT_USER = "pOTUser";
    case OT_PASSWORD = "pOTPassword";
    case LANGUAGE_CODE = "pLanguagecode";

    case FAILURE = "pFehler";
}