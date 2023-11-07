<?php

namespace Waka\Docser\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\PluginManager;
use Winter\Storm\Support\Collection;
use Waka\Docser\Models\Appdoc;
use Waka\Docser\Models\CategoryDoc;

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
        $this->vars['systemsNav'] = $this->getNavigation();
    }

    public function index($pageCode = null)
    {
    }

    public function preview($slug)
    {
        $docData = Appdoc::where('slug', $slug)->first();
        if ($docData) {
            $this->vars['title'] = $docData->name;
            $content = $this->renderManualDoc($docData);
        } else {
            $content = $this->renderDoc($slug);
        }
        $this->vars['content'] = $this->addIds($content);
    }

    private function addIds($html)
    {
        trace_log($html);
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        // Récupérez tous les éléments de titre (h1, h2, etc.)
        $xpath = new \DOMXPath($dom);
        $titles = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');
        trace_log($titles);

        foreach ($titles as $title) {
            // Créez un ID unique. Ici, nous utilisons le contenu du titre, mais assurez-vous qu'il est unique.
            $id = \Str::slug($title->textContent);
            $title->setAttribute('id', $id);
        }
        $html = $dom->saveHTML();
        // Sauvegardez le HTML modifié
        return $html;
    }

    public function getNavigation()
    {
        //Récupération des doc de la BDD
        $bddDoc = Appdoc::get(['name', 'slug', 'description', 'permissions', 'category_slug'])->groupBy('category_slug');
        $bddDoc = $this->filterByPermission($bddDoc)->toArray();
        //Récuperation de la doc auto des plugins
        $pluginDoc = $this->docsCollection->sortBy('order');
        $pluginDoc->transform(function ($element, $key) {
            $element['slug'] = $key;
            return $element;
        });
        $pluginDoc = $this->filterByPermission($pluginDoc);
        $pluginDoc = $pluginDoc->sortBy('sort_order')->groupBy('category_slug')->toArray();
        $allDocs = array_merge_recursive($bddDoc, $pluginDoc);
        $navs = CategoryDoc::get(['name', 'slug'])->toArray();
        $finalNav = [];
        foreach ($navs as $navData) {
            $navDataSlug =  $navData['slug'];
            $navDataTitle =  $navData['name'];
            $finalElements = $allDocs[$navDataSlug] ?? null;
            if ($finalElements) {
                $finalNav[$navDataTitle] = $finalElements;
            }
        }
        return $finalNav;
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
