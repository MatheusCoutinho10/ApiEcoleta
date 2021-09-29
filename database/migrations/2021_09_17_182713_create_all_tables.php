<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     //Função de criação das tabelas
    public function up()
    {
        //Tabelas ligadas aos Usuários
        //Usuários
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->default('default.png');
            $table->string('email')->unique();
            $table->string('password');
        });

        //Favoritos
        Schema::create('userfavorites', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_coop');
        });

        //Apontamentos
        Schema::create('userappointments', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_coop');
            $table->datetime('ap_datetime');
        });

        //Tabelas ligadas as Cooperativas
        //Cooperativas
        Schema::create('coops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->default('default.png');
            $table->float('stars')->default(0);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });

        //Fotos
        Schema::create('coopphotos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_coop');
            $table->string('url');
        });

        //Notas
        Schema::create('coopreviews', function (Blueprint $table) {
            $table->id();
            $table->integer('id_coop');
            $table->float('rate');
        });

        //Depoimentos
        Schema::create('cooptestimonials', function (Blueprint $table) {
            $table->id();
            $table->integer('id_coop');
            $table->string('name');
            $table->float('rate');
            $table->string('body');
        });

        //Disponibilidade
        Schema::create('coopavailability', function (Blueprint $table) {
            $table->id();
            $table->integer('id_coop');
            $table->integer('weekday');
            $table->text('hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('userfavorites');
        Schema::dropIfExists('userappointments');
        Schema::dropIfExists('coops');
        Schema::dropIfExists('coopphotos');
        Schema::dropIfExists('coopreviews');
        Schema::dropIfExists('cooptestimonials');
        Schema::dropIfExists('coopavailability');
    }
}