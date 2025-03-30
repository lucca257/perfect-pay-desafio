<?php

namespace App\Enums;

enum StatusTransacaoEnum: int
{
    case PENDENTE = 1;
    case APROVADO = 2;
    case REJEITADO = 3;
    case CANCELADO = 4;
    case REEMBOLSO = 5;

    public function status(): string
    {
        return match ($this) {
            self::PENDENTE => 'Pendente',
            self::APROVADO => 'Aprovado',
            self::REJEITADO => 'Rejeitado',
            self::CANCELADO => 'Cancelado',
            self::REEMBOLSO => 'Reembolso',
        };
    }

    public function descricao(): string
    {
        return match ($this) {
            self::PENDENTE => 'A transação está aguardando confirmação.',
            self::APROVADO => 'A transação foi aprovada.',
            self::REJEITADO => 'A transação foi rejeitada.',
            self::CANCELADO => 'A transação foi cancelada.',
            self::REEMBOLSO => 'A transação foi reembolsada.',
        };
    }
}
