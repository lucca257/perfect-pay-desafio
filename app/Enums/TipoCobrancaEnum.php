<?php

namespace App\Enums;

enum TipoCobrancaEnum: int
{
    case A_VISTA = 1;
    case PARCELAMENTO = 2;
    case RECORRENTE = 3;

    public function tipoPorProvedor(ProvedoresPagamentoEnum $provedor): string
    {
        return match ([$this, $provedor])
        {
            [self::A_VISTA, ProvedoresPagamentoEnum::ASAAS] => 'DETACHED',
            [self::PARCELAMENTO, ProvedoresPagamentoEnum::ASAAS] => 'INSTALLMENT',
            [self::RECORRENTE, ProvedoresPagamentoEnum::ASAAS] => 'RECURRENT',
        };
    }

    public function descricao(): string
    {
        return match ($this)
        {
            self::A_VISTA => 'À vista',
            self::PARCELAMENTO => 'Parcelamento',
            self::RECORRENTE => 'Recorrente',
        };
    }
}
