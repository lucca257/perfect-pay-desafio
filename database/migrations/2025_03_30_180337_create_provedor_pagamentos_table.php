<?php

use App\Enums\ProvedoresPagamentoEnum;
use App\Models\ProvedorPagamento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provedor_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        ProvedorPagamento::create([
            'id' => ProvedoresPagamentoEnum::ASSAS->value,
            'nome' => ProvedoresPagamentoEnum::ASSAS
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provedor_pagamentos');
    }
};
