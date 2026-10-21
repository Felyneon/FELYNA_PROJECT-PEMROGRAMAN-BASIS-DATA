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
        Schema::create('barang', function (Blueprint $table) {
           $table->id('idbarang');
            $table->string('jenis', 100);
            $table->string('nama', 45);
            $table->tinyInteger('status')->default(1);
            $table->integer('harga');
            
            // Foreign Key ke tabel satuan
            $table->unsignedBigInteger('idsatuan');
            $table->foreign('idsatuan')
                  ->references('idsatuan')
                  ->on('satuan')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
