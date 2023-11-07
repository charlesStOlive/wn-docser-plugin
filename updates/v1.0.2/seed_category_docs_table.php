<?php namespace Waka\Docser\Updates;

use Seeder;
use Waka\Docser\Models\CategoryDoc;
/**
 * some_upgrade_file.php
 */
return new class extends Seeder
{
    public function run()
    {
        CategoryDoc::create(['name' => 'Operation', 'slug' => 'operation']);
        CategoryDoc::create(['name' => 'Workflows', 'slug' => 'workflows']);
        CategoryDoc::create(['name' => 'Administration', 'slug' => 'administration']);
        CategoryDoc::create(['name' => 'Web Integration', 'slug' => 'web_integration']);
        CategoryDoc::create(['name' => 'Technical', 'slug' => 'technical']);
        CategoryDoc::create(['name' => 'Modules', 'slug' => 'modules']);
    }
};