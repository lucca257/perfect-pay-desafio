<?php

namespace App\Http\Resources;

use App\Enums\MetodosPagamentoEnum;
use App\Models\StatusTransacao;
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
        return [
            'id' => $this->id,
            'identificador' => $this->identificador,
            'detalhes' => $this->detalhes,
            'historico' => $this->historico,
            'metodos_pagamento' => MetodosPagamentoEnum::from($this->metodos_pagamento_id),
            'status' => StatusTransacao::from($this->status_transacao_id),
        ];
    }
}
