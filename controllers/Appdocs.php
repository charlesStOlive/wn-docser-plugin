<?php namespace Waka\Docser\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Appdoc Back-end Controller
 */
class Appdocs extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Waka.Wutils.Behaviors.BtnsBehavior',
    ];
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $btnsConfig = 'config_btns.yaml';

    public $requiredPermissions = ['waka.docser.edit'];
    //FIN DE LA CONFIG AUTO
    //startKeep/
    
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.Docser', 'Appdocs');
    }

    

        //endKeep/
}

