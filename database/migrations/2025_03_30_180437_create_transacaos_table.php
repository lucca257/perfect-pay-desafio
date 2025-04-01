<?php

use App\Models\Cliente;
use App\Models\MetodosPagamento;
use App\Models\StatusTransacao;
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
        Schema::create('transacaos', function (Blueprint $table) {
            $table->id();
            $table->string('identificador')->index();
            $table->foreignIdFor(Cliente::class)->nullable();
            $table->foreignIdFor(MetodosPagamento::class);
            $table->foreignIdFor(StatusTransacao::class);
            $table->json('detalhes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacaos');
    }
};
