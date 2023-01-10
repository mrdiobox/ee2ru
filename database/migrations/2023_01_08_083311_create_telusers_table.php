<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telusers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('username', 255);
            $table->string('photo_url', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telusers');
    }
}
