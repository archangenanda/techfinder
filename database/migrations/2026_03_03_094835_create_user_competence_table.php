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
        Schema::create('user_competence', function (Blueprint $table) {
            $table->string('code_user');
            $table->unsignedInteger('code_comp');
            $table->foreign('code_user')->references('code_user')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('code_comp')->references('code_comp')->on('competences')->onDelete('cascade');
            $table->primary(['code_user', 'code_comp']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_competence');
    }
};
