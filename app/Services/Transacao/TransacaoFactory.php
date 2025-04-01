<?php

namespace App\Services\Transacao;

use App\Enums\ProvedoresPagamentoEnum;

class TransacaoFactory
{
    public static function provider(ProvedoresPagamentoEnum $provedoresPagamentoEnum): AsaasTransacaoProvider
    {
        return match ($provedoresPagamentoEnum) {
            ProvedoresPagamentoEnum::ASAAS => new AsaasTransacaoProvider(),
        };
    }
}
