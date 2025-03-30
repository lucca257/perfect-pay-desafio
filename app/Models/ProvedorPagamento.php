<?php

namespace App\Models;

use Database\Factories\ProvedorPagamentoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvedorPagamento extends Model
{
    /** @use HasFactory<ProvedorPagamentoFactory> */
    use HasFactory;

    protected $guarded = [];
}
