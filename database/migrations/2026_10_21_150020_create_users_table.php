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
        Schema::create('user', function (Blueprint $table) {
            $table->id('iduser');
            $table->string('username', 45)->unique();
            $table->string('password', 100);
            
            // Foreign Key ke tabel role
            $table->unsignedBigInteger('idrole');
            $table->foreign('idrole')
                  ->references('idrole')
                  ->on('role')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
