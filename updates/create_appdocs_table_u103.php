<?php namespace Waka\Docser\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateAppdocsTableU103 extends Migration
{
    public function up()
    {
        Schema::table('waka_docser_appdocs', function ($table) {
            $table->renameColumn('roles', 'permissions');
        });
    }

    public function down()
    {
        Schema::table('waka_docser_appdocs', function ($table) {
            $table->renameColumn('permissions', 'roles');
        });
    }
}