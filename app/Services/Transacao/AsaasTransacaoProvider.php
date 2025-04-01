<?php

namespace App\Services\Transacao;

use App\DTOs\Pagamento\CriarPagamentoDto;
use App\DTOs\Pagamento\CriarTransacaoDto;
use App\Enums\ProvedoresPagamentoEnum;
use App\Enums\StatusTransacaoEnum;
use App\Models\Transacao;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasTransacaoProvider implements TransacaoProvider
{
    /**
     * @param  CriarTransacaoDto  $criarTransacaoDto
     * @return Transacao
     * @throws ConnectionException
     */
    public function criar(CriarTransacaoDto $criarTransacaoDto): Transacao
    {
        $dados = [
            "billingType" => $criarTransacaoDto->metodoPagamento->tipoPorProvedor(ProvedoresPagamentoEnum::ASAAS),
            "chargeType" => $criarTransacaoDto->tipoCobrancaEnum->tipoPorProvedor(ProvedoresPagamentoEnum::ASAAS),
            "name" => $criarTransacaoDto->titulo,
            "description" => $criarTransacaoDto->descricao,
            "value" => $criarTransacaoDto->valor,
            "notificationEnabled" => false,
            "dueDateLimitDays" => 10,
            'maxInstallmentCount' => 1
        ];

        $resposta = $this->request()->post('/v3/paymentLinks', $dados);

        if ($resposta->clientError()) {
            Log::error('Erro ao criar pagamento no provedor asaas');
            report($resposta->object());
            throw new \RuntimeException('Erro ao criar pagamento, tente novamente!', $resposta->object());
        }

        return $this->save($resposta->object(), $criarTransacaoDto);
    }

    public function pagar(array $payload)
    {
        return DB::transaction(function () use($payload) {
            $identificador = $payload['payment']['paymentLink'];
            $transacao = Transacao::where('identificador', $identificador)->first();
            $status = StatusTransacaoEnum::APROVADO->value;

            $transacao->update(['status_transacao_id' => $status]);
            $transacao->historicos()->create([
                'status_transacao_id' => $status,
                'payload' => $payload,
            ]);
            return $transacao;
        });
    }

    /**
     * @param  object  $resposta
     * @param  CriarTransacaoDto  $criarTransacaoDto
     * @return Transacao
     */
    private function save(object $resposta, CriarTransacaoDto $criarTransacaoDto) : Transacao
    {
        return DB::transaction(function () use ($resposta, $criarTransacaoDto){
            $status = StatusTransacaoEnum::PENDENTE->value;
            $transacao = Transacao::create([
                'identificador' => $resposta->id,
                'cliente_id' => $criarTransacaoDto->cliente_id,
                'metodos_pagamento_id' => $criarTransacaoDto->metodoPagamento->value,
                'status_transacao_id' => $status,
                'detalhes' => collect([
                    'titulo' => $criarTransacaoDto->titulo,
                    'descricao' => $criarTransacaoDto->descricao,
                    'valor' => $criarTransacaoDto->valor,
                    'tipo_cobranca' => $criarTransacaoDto->tipoCobrancaEnum->descricao(),
                    'url_pagamento' => $resposta->url
                ])
            ]);

            $transacao->historico()->create([
                'status_transacao_id' => $status,
                'payload' => $resposta,
            ]);

            return $transacao->load('historico');
        });
    }

    /**
     * @return PendingRequest
     */
    private function request(): PendingRequest {
        return Http::baseUrl('https://api-sandbox.asaas.com')->withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'access_token' => config('pagamento.asaas.access_token')
        ]);
    }
}
