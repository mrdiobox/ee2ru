<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTeluserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telusers', function (Blueprint $table) {
            $table->string('first_name', 255)->nullable()->change();
            $table->string('last_name', 255)->nullable()->change();
            $table->string('username', 255)->nullable()->change();
            $table->string('photo_url', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
