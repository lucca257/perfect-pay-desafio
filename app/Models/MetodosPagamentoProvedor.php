<?php

namespace App\Models;

use Database\Factories\MetodosPagamentoProvedorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodosPagamentoProvedor extends Model
{
    /** @use HasFactory<MetodosPagamentoProvedorFactory> */
    use HasFactory;

    protected $table = 'metodos_pagamento_provedor';
    protected $guarded = [];
}
