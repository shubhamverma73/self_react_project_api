<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateTimeFieldInRsoUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rso_user', function (Blueprint $table) {
            $table->date('date')->after('token');
            //$table->string('time')->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rso_user', function (Blueprint $table) {
            $table->dropColumn('date');
            //$table->dropColumn('time');
        });
    }
}
