<?php

use App\Enums\StatusTransacaoEnum;
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
        Schema::create('status_transacao', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->timestamps();
        });

        foreach (StatusTransacaoEnum::cases() as $status) {
            StatusTransacao::create([
                'id' => $status->value,
                'status' => $status->status(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_transacao');
    }
};
