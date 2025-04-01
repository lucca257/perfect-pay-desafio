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

    public function tipoPorProvedor(ProvedoresPagamentoEnum $provedor): string
    {
        return match ([$this, $provedor])
        {
            [self::BOLETO, ProvedoresPagamentoEnum::ASAAS] => 'BOLETO',
            [self::PIX, ProvedoresPagamentoEnum::ASAAS] => 'PIX',
            [self::CREDITO, ProvedoresPagamentoEnum::ASAAS] => 'CREDIT_CARD',
        };
    }
}
