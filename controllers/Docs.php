<?php

namespace Waka\Docser\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\PluginManager;
use Winter\Storm\Support\Collection;
use Waka\Docser\Models\Appdoc;

/**
 * Docs Back-end Controller
 */
class Docs extends Controller
{
    public $layout = 'empty';
    private Collection $docsCollection;
    private Collection $partsCollection;



    public function __construct()
    {
        parent::__construct();
        $this->addCss('/plugins/waka/docser/assets/css/docs.css');
        BackendMenu::setContext('Waka.Docser', 'docs');
        $this->getDocsData();
        $this->vars['systemDocsNav'] = $this->getDocsNavigation();
        $this->vars['manualDocsNav'] = $this->getManualDocsNavigation();
    }

    public function index($pageCode = null)
    {
    }

    public function system_preview($pageCode = null)
    {
        $content = $this->renderDoc($pageCode);
        $this->vars['content'] = $content;
    }

    public function manual_preview($slug)
    {
        $docData = Appdoc::where('slug', $slug)->first();
        $this->pageTitle = $this->vars['title'] =  $docData->name;
        $content = $this->renderManualDoc($docData);
        $this->vars['content'] = $content;
    }


    /**
     * getDocsNavigation 
     * les docs manuels
     * Attention le filtre s'effectue sur les id de roles
     */
    public function getManualDocsNavigation()
    {
        $docs = Appdoc::get(['name', 'slug', 'description', 'permissions']);
        //trace_log($docs->toArray());
        $docs = $this->filterByPermission($docs);
        //trace_log($docs->toArray());
        return $docs;
    }


    /**
     * getDocsNavigation 
     * les docs automatiques des plugins
     * Attention le filtre s'effectue sur les codes de plugin via l'information permission
     */
    public function getDocsNavigation()
    {
        $docsdata = $this->docsCollection->sortBy('order');
        $docsdata = $this->filterByPermission($docsdata);
        $docsdata = $docsdata->sortBy('group')->groupBy('group');
        return $docsdata->toArray();
    }

    private function filterByPermission($datas)
    {
        return $datas->filter(function ($item) {
            $permissions = $item['permissions'] ?? false;
            if ($permissions) {
                if ($this->user->hasAccess($permissions, false)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        });
    }

    private function getDocsData()
    {
        $this->docsCollection = new Collection();
        $this->partsCollection = new Collection();
        $plugins = PluginManager::instance()->getPlugins();
        $docCollection = new \Winter\Storm\Support\Collection();
        foreach ($plugins as $plugin) {
            $pluginPath = $plugin->getPluginPath();
            $yamlPath = $pluginPath . '/wakadocs.yaml';
            $yamlFile = \File::exists($yamlPath);
            //trace_log($yamlPath." :  ".$yamlFile);
            if ($yamlFile) {
                $datas = \Yaml::parseFile($yamlPath);
                $files = $datas['files'] ?? [];
                foreach ($files as $key => $file) {
                    $objet = $file;
                    $objet['code'] = $key;
                    $objet['path'] = $pluginPath . '/docs/' . $key . '.md';
                    $this->docsCollection->put($key, $objet);
                }
                $parts = $datas['parts'] ?? [];
                foreach ($parts as $key => $part) {
                    $objet = $part;
                    $objet['code'] = $part['code'] ?? $key;
                    $objet['path'] = $pluginPath . '/docs/parts/' . $key . '.md';
                    $this->partsCollection->push($objet);
                }
            }
        }
    }



    public function getDocFromCode($docId)
    {
        return $this->docsCollection->get($docId);
    }

    public function renderDoc($docId)
    {
        if (!$docId) {
            $docId = 'utils_install';
        }
        $docData = $this->getDocFromCode($docId);
        $docPath = $docData['path'];
        $fileContent = \File::get($docPath);
        //Modification des urls des images
        $regex = '/!\[(.*)\]\(([^ ]+)\)/m';
        $basePath = '/plugins/' . $docData['relativePath'];
        $subst = '![$1](' . $basePath . htmlspecialchars('/$2)');
        $result = preg_replace($regex, $subst, $fileContent);
        //
        $result = $this->integrateSubart($result);
        return \Markdown::parse($result);
    }

    public function integrateSubart($text)
    {
        $regex = '/<!--includepart\[(.*?)\]-->/m';
        return preg_replace_callback($regex, [&$this, 'insertpart'], $text);
    }

    public function insertpart($matches)
    {
        $partData = $matches[1] ?? null;
        if (!$partData) {
            return null;
        }
        $partsToReturn = null;
        //Si il y a plusieurs parts
        $arrayPart = explode(',', $partData);
        foreach ($arrayPart as $partName) {
            if ($partName) {
                $parts = $this->partsCollection->where('code', $partName)->toArray();
                $marpartContentkDown = null;
                foreach ($parts as $part) {
                    $partsToReturn .= \File::get($part['path']);
                }
            }
        }
        return $partsToReturn;
    }


    public function renderManualDoc($data)
    {
        $fileContent = \Markdown::parse($data->content);
        return $fileContent;
    }
}
