<?php

namespace App\Enums;

enum ProvedoresPagamentoEnum: int
{
    case ASAAS = 1;

    public static function getFromString(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if (strtoupper($value) === $case->name) {
                return $case;
            }
        }

        return null;
    }
}
