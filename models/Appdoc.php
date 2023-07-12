<?php

namespace Waka\Docser\Models;

use Model;
use Carbon\Carbon;
use System\Classes\PluginManager;

/**
 * appdoc Model
 */

class Appdoc extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    use \Winter\Storm\Database\Traits\Sortable;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_docser_appdocs';


    /**
     * @var array Guarded fields
     */
    protected $guarded = ['id'];

    /**
     * @var array Fillable fields
     */
    //protected $fillable = [];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'name' => 'required',
        'description' => 'max: 200',
        'content' => 'required',
    ];

    public $customMessages = [];

    /**
     * @var array attributes send to datasource for creating document
     */
    public $attributesToDs = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [
        'permissions',
    ];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array Spécifié le type d'export à utiliser pour chaque champs
     */
    public $importExportConfig = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    //startKeep/

    /**
     *EVENTS
     **/

    /**
     * LISTS
     **/

    public function listAllPermissions()
    {

        $bundles = PluginManager::instance()->getRegistrationMethodValues('registerPermissions');
        if (!$bundles) {
            return [];
        }
        $permissions = [];
        foreach ($bundles as $plugin) {
            if (is_array($plugin)) {
                foreach ($plugin as $code => $details) {
                    $label = $details['label'] ?? null;
                    $tab = $details['tab'] ?? null;
                    if ($label) {
                        $permissions[$code] = \Lang::get($tab).': '.\Lang::get($label);
                    }
                }
            }
        }
        return $permissions;
    }

    /**
     * GETTERS
     **/

    /**
     * SCOPES
     */

    /**
     * SETTERS
     */

    /**
     * FILTER FIELDS
     */

    /**
     * OTHERS
     */

    //endKeep/
}
