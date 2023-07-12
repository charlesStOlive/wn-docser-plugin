<?php namespace Waka\Docser\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateAppdocsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_docser_appdocs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('limit_users')->nullable();
            $table->text('roles')->nullable();
            $table->string('description')->nullable();
            $table->text('content')->nullable();
            //reorder
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_docser_appdocs');
    }
}