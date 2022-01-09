<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFkToInstances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->dropForeign('instances_instance_type_id_foreign');
            $table->dropColumn('instance_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->unsignedBigInteger('instance_type_id');
            $table->foreign('instance_type_id')
                  ->references('id')->on('instance_types')
                  ->onDelete('cascade');
        });
    }
}
