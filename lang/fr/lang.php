<?php

return [
    'controllers' => [
        'appdocs' => [
            'label' => 'Appdocs',
            'app_usage' => 'Doc utilisation/intégration',
            'tecnical_doc' => 'Docs techniques',
            'category_docs' => 'Catégories',
        ],
        'text_intro' => 'Choisissez un élément dans le menu',
        'categorydocs' => [
            'label' => 'Category Docs',
        ],
    ],
    'models' => [
        'appdoc' => [
            'content' => 'Utilise une source de données',
            'description' => 'Description',
            'limit_users' => 'Limité à certains rôles',
            'menu_appdocs' => 'Documentations',
            'menu_appdocs_description' => 'Créer et modifier des pages de documentation',
            'name' => 'Nom de la page',
            'permissions' => 'Permissions',
            'slug' => 'Slug de la page',
            'title_front' => 'Documentation',
            'label' => 'Doc',
            'roles' => 'Rôles',
            'category_slug' => 'Catégorie',
            'sort_order' => 'Ordre',
        ],
        'general' => [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
        'categorydoc' => [
            'name' => 'Nom catégorie',
            'label' => 'Category Doc',
            'slug' => 'Code',
            'sort_order' => 'Ordre',
        ],
    ],
];
