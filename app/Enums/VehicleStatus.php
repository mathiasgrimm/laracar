<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Ativo = 'ativo';
    case Vendido = 'vendido';
    case Pausado = 'pausado';
}
