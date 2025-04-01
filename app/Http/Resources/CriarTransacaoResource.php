<?php

namespace App\Http\Resources;

use App\Enums\MetodosPagamentoEnum;
use App\Enums\StatusTransacaoEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CriarTransacaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = StatusTransacaoEnum::from($this->status_transacao_id);
        return [
            'id' => $this->id ?? null,
            'identificador' => $this->identificador,
            'detalhes' => $this->detalhes,
            'historico' => $this->historico,
            'metodos_pagamento' => MetodosPagamentoEnum::from($this->metodos_pagamento_id),
            'status' => $status->status(),
            'status_descricao' => $status->descricao()
        ];
    }
}
