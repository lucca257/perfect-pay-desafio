<?php

namespace App\Enums;

enum MetodosPagamentoEnum: int
{
    case BOLETO = 1;
    case PIX = 2;
    case CREDITO = 3;

    public function tipo(): string
    {
        return match ($this)
        {
            self::BOLETO => 'Boleto',
            self::PIX => 'Pix',
            self::CREDITO => 'Crédito',
        };
    }
}
