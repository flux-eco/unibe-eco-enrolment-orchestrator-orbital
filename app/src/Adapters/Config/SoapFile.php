<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Adapters\Config;

enum SoapFile: string
{
    case HELPTABLE = "helptable";
    case DEGREE_PROGRAMME = "degree-programme";


    public function toPath() : string
    {
        return match ($this) {
            self::HELPTABLE => 'Studis/Helptable.svc?wsdl',
            self::DEGREE_PROGRAMME => 'Studis/Studiengang.svc?wsdl'
        };
    }
}