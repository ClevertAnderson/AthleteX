<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->string('place_of_birth')->nullable()->after('age');
        });
    }

    public function down()
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn('place_of_birth');
        });
    }
};