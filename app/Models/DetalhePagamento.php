<?php

namespace App\Models;

use Database\Factories\DetalhePagamentoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalhePagamento extends Model
{
    /** @use HasFactory<DetalhePagamentoFactory> */
    use HasFactory;

    protected $guarded = [];
}
