<?php

namespace App\Enums;

enum FuelType: string
{
    case Gasolina = 'gasolina';
    case Etanol = 'etanol';
    case Flex = 'flex';
    case Diesel = 'diesel';
    case Eletrico = 'eletrico';
    case Hibrido = 'hibrido';
}
