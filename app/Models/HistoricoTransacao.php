<?php

namespace App\Models;

use Database\Factories\HistoricoTransacaoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoTransacao extends Model
{
    /** @use HasFactory<HistoricoTransacaoFactory> */
    use HasFactory;

    protected $table = 'historico_transacao';
    protected $guarded = [];
    protected $casts = [
        'payload' => 'collection'
    ];

    public function transacao(): BelongsTo
    {
        return $this->belongsTo(Transacao::class);
    }
}
