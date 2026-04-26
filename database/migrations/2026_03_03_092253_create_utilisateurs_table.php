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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->string('code_user')->primary();
            $table->string('nom_user');
            $table->string('prenom_user');
            $table->string('login_user')->unique();
            $table->string('password_user');
            $table->string('tel_user');
            $table->enum('sexe_user', ['M', 'F']);
            $table->enum('role_user', ['admin', 'technicien', 'client'])->default('client');
            $table->enum('etat_user', ['actif', 'inactif', 'suspendu'])->default('inactif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
