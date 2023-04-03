<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\Portrait\Types;

interface Salutation
{
    public function getId(): string;
    public function getLabel(): Label;
}