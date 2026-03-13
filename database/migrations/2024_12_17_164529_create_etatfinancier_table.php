<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtatfinancierTable extends Migration
{
    public function up()
    {
        Schema::create('etatfinancier', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id'); // Assurez-vous que la table clients existe
            $table->string('info');
            $table->string('etat_301');
            $table->string('etat_302');
            $table->string('tee_rme');
            $table->string('balance');
            $table->string('bilan');
            $table->string('pv');
            $table->string('rapport');
            $table->string('facture');
            $table->string('valid_client');
            $table->string('visa');
            $table->string('depot_ligne');
            $table->string('depot_physique');
            $table->string('said');
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('etatfinancier');
    }
}
