<?php

namespace App\Http\Controllers;

use App\DTOs\Pagamento\CriarTransacaoDto;
use App\Enums\MetodosPagamentoEnum;
use App\Enums\ProvedoresPagamentoEnum;
use App\Enums\TipoCobrancaEnum;
use App\Http\Requests\CriarTransacaoRequest;
use App\Http\Resources\CriarTransacaoResource;
use App\Services\Transacao\TransacaoFactory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;

class TransacaoController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function store(CriarTransacaoRequest $request)
    {
        $dto = new CriarTransacaoDto(
            $request->titulo,
            $request->descricao,
            $request->dataLimite,
            $request->valor,
            MetodosPagamentoEnum::tryFrom($request->metodoPagamento),
            TipoCobrancaEnum::tryFrom($request->tipoCobranca),
            $request->clienteId
        );

        $provedor = TransacaoFactory::provider(ProvedoresPagamentoEnum::ASAAS);
        return new CriarTransacaoResource($provedor->criar($dto));
    }

    public function webhook(Request $request, string $provider)
    {
        if (!ProvedoresPagamentoEnum::getFromString($provider)) {
            return response()->json(['error' => 'Provedor inválido'], 500);
        }

        $provedor = TransacaoFactory::provider(ProvedoresPagamentoEnum::ASAAS);
        $provedor->pagar($request->all());
        return response()->json();
    }

    public function index()
    {
        $provedor = TransacaoFactory::provider(ProvedoresPagamentoEnum::ASAAS);
        $transacoes = $provedor->listar();

        return CriarTransacaoResource::collection($transacoes);
    }
}
