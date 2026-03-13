<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulairesTable extends Migration
{
    public function up()
    {
        Schema::create('formulaire', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email');
            $table->string('critere');
            $table->text('commentaire');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('formulaire');
    }
}