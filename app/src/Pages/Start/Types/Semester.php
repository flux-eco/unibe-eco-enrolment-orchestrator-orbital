<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Pages\Start\Types;
interface Semester
{
    public function getId(): string;
    public function getLabel(): Label;
}