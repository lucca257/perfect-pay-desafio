<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MetodosPagamento extends Model
{
    protected $guarded = [];

    public function provedores(): BelongsToMany
    {
        return $this->belongsToMany(ProvedorPagamento::class, 'metodos_pagamento_provedor');
    }
}
