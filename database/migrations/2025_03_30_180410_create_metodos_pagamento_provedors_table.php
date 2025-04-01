<?php

use App\Enums\MetodosPagamentoEnum;
use App\Enums\ProvedoresPagamentoEnum;
use App\Models\MetodosPagamento;
use App\Models\MetodosPagamentoProvedor;
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
        Schema::create('metodos_pagamento_provedor', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProvedorPagamento::class);
            $table->foreignIdFor(MetodosPagamento::class);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            //garante que 1 provedor tenha apenas 1 metodo de pagamento
            $table->unique(['provedor_pagamento_id', 'metodos_pagamento_id']);
        });

        MetodosPagamentoProvedor::insert([
            [
                'provedor_pagamento_id' => ProvedoresPagamentoEnum::ASAAS->value,
                'metodos_pagamento_id' => MetodosPagamentoEnum::BOLETO->value
            ],
            [
                'provedor_pagamento_id' => ProvedoresPagamentoEnum::ASAAS->value,
                'metodos_pagamento_id' => MetodosPagamentoEnum::PIX->value
            ],
            [
                'provedor_pagamento_id' => ProvedoresPagamentoEnum::ASAAS->value,
                'metodos_pagamento_id' => MetodosPagamentoEnum::CREDITO->value
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodos_pagamento_provedors');
    }
};
