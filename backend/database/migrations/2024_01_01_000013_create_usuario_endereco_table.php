<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_endereco', function (Blueprint $table) {
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('endereco_id')->constrained('enderecos')->cascadeOnDelete();
            $table->primary(['usuario_id', 'endereco_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_endereco');
    }
};
