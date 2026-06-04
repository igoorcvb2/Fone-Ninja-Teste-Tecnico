<?php

namespace App\Enums;

enum StatusVenda: string
{
    case Concluida = 'CONCLUIDA';
    case Cancelada = 'CANCELADA';

    public function label(): string
    {
        return match ($this) {
            self::Concluida => 'Concluída',
            self::Cancelada => 'Cancelada',
        };
    }
}
