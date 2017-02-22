<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('QuestConfig', function (Blueprint $table) {
            $table->increments('id');
            //$table->timestamps();

	        $table->string("conf_title");
	        $table->string("conf_name");
	        $table->text("conf_content");
	        $table->integer("conf_order")->unsigned();
	        $table->string("conf_tips");
	        $table->string("field_type");
	        $table->string("field_value");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('QuestConfig');
    }
}
