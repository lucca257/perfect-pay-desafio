<?php

namespace App\Models;

use Database\Factories\ProvedorPagamentoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProvedorPagamento extends Model
{
    /** @use HasFactory<ProvedorPagamentoFactory> */
    use HasFactory;

    protected $guarded = [];

    public function transacoes()
    {
        return $this->hasMany(Transacao::class);
    }

    public function metodos(): BelongsToMany
    {
        return $this->belongsToMany(MetodosPagamento::class, 'metodos_pagamento_provedor');
    }
}
