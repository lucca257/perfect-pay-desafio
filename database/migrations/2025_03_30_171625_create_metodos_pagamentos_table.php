<?php

use App\Enums\MetodosPagamentoEnum;
use App\Models\MetodosPagamento;
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
        Schema::create('metodos_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->timestamps();
        });

        foreach (MetodosPagamentoEnum::cases() as $metodo) {
            MetodosPagamento::create([
                'id' => $metodo->value,
                'tipo' => $metodo->tipo(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodos_pagamentos');
    }
};
