<?php

namespace App\Enums;

enum FieldRequirementEnum: string
{
    case REQUIRED = 'required';  // Field must always be present
    case SOMETIMES = 'sometimes'; // Field may be present
}