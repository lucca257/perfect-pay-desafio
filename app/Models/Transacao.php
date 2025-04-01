<?php

namespace App\Models;

use Database\Factories\TransacaoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transacao extends Model
{
    /** @use HasFactory<TransacaoFactory> */
    use HasFactory;

    protected $guarded = [];
    protected $casts = ['detalhes' => 'collection'];

    public function historico(): HasMany
    {
        return $this->hasMany(HistoricoTransacao::class);
    }
}
