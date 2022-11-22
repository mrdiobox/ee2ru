<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('narva', function (Blueprint $table) {
            $table->id();
            $table->integer('lql_ab');
            $table->integer('nvl_ab');
            $table->integer('nvf_ab');
            $table->datetime('frt_ab');
            $table->datetime('lagh_ab');

            $table->integer('nvl_c');
            $table->integer('nvf_c');
            $table->datetime('frt_c');
            $table->datetime('lagh_c');

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
        Schema::dropIfExists('narvas');
    }
};
