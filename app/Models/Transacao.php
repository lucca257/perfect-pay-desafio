<?php

namespace App\Models;

use Database\Factories\TransacaoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    /** @use HasFactory<TransacaoFactory> */
    use HasFactory;

    protected $guarded = [];
}
