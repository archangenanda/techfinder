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
        Schema::create('intervention', function (Blueprint $table) {
            $table->increments('code_int');
            $table->date('date_int')->default(now());
            $table->unsignedSmallInteger('note_int')->default(0);
            $table->text('commentaire_int')->nullable();
            $table->string('code_user_client', 15);
            $table->string('code_user_techn', 15);
            $table->unsignedInteger('code_comp');
            $table->foreign('code_user_client')->references('code_user')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('code_user_techn')->references('code_user')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('code_comp')->references('code_comp')->on('competences')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention');
    }
};
