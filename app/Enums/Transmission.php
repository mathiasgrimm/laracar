<?php

namespace App\Enums;

enum Transmission: string
{
    case Manual = 'manual';
    case Automatico = 'automatico';
    case Cvt = 'cvt';
    case Automatizado = 'automatizado';
}
