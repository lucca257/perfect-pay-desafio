<?php

namespace App\Services\Transacao;

use App\DTOs\Pagamento\CriarTransacaoDto;
use App\Models\Transacao;

interface TransacaoProvider
{
    /**
     * @param  CriarTransacaoDto  $criarTransacaoDto
     * @return Transacao
     */
    public function criar(CriarTransacaoDto $criarTransacaoDto): Transacao;

    public function pagar(array $payload): Transacao;

    public function listar();
}
