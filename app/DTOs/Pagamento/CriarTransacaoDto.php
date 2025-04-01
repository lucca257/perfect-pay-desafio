<?php

namespace App\DTOs\Pagamento;

use App\Enums\MetodosPagamentoEnum;
use App\Enums\TipoCobrancaEnum;

class CriarTransacaoDto
{
    public function __construct(
        public string $titulo,
        public string $descricao,
        public string $dataLimite,
        public float $valor,
        public MetodosPagamentoEnum $metodoPagamento,
        public TipoCobrancaEnum $tipoCobrancaEnum,
        public ?int $cliente_id = null
    ) {}
}
