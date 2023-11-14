<?php

return [
    'controllers' => [
        'appdocs' => [
            'category_docs' => 'Categories',
            'label' => 'Appdocs',
        ],
        'categorydocs' => [
            'label' => 'Category Docs',
        ],
        'text_intro' => 'Choose an item from the menu',
    ],
    'models' => [
        'appdoc' => [
            'category_slug' => 'Category',
            'content' => 'Uses a data source',
            'description' => 'Description',
            'label' => 'Doc',
            'limit_users' => 'Limited to certain roles',
            'menu_appdocs' => 'Documentations',
            'menu_appdocs_description' => 'Create and modify documentation pages',
            'name' => 'Page name',
            'permissions' => 'Permissions',
            'roles' => 'Roles',
            'slug' => 'Page slug',
            'title_front' => 'Documentation',
        ],
        'categorydoc' => [
            'label' => 'Category Doc',
            'name' => 'Category name',
            'slug' => 'Code',
            'sort_order' => 'Order',
        ],
        'general' => [
            'created_at' => 'Created on',
            'id' => 'ID',
            'updated_at' => 'Updated on',
        ],
    ],
];
