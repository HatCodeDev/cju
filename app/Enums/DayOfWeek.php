<?php

declare(strict_types=1);

namespace App\Enums;

enum DayOfWeek: int
{
    case Lunes = 1;
    case Martes = 2;
    case Miercoles = 3;
    case Jueves = 4;
    case Viernes = 5;
    case Sabado = 6;
    case Domingo = 7;
}
