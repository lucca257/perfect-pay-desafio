<?php

use App\Models\Transacao;
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
        Schema::create('detalhe_pagamento', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Transacao::class);
            $table->json('dados_pagamento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalhe_pagamento');
    }
};
