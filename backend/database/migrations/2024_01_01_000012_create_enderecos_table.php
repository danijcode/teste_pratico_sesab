<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->string('logradouro', 200);
            $table->string('cep', 9); // formato: 00000-000
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
