<?php

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
        Schema::create('tbl_produtos', function (Blueprint $table) {
            $table->id('produtos_id');
            $table->string('nomeProduto',50);
            $table->string('descricaoProduto',100);
            $table->string('secao',20);
            $table->string('qtd',100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_produtos');
    }
};
