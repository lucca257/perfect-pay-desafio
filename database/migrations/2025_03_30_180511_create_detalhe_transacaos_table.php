<?php

use App\Models\StatusTransacao;
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
        Schema::create('historico_transacao', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Transacao::class);
            $table->foreignIdFor(StatusTransacao::class);
            $table->json('payload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_transacao');
    }
};
