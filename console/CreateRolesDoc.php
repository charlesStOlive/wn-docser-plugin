<?php

namespace Waka\Docser\Console;


use System\Console\BaseScaffoldCommand;
use System\Classes\PluginManager;

class CreateRolesDoc extends BaseScaffoldCommand
{
    /**
     * @var string The console command name.
     */
    protected static $defaultName = 'docser:createrolesdoc';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'docser:createrolesdoc
        {plugin : Le nom du plugin qui va recevoir la doc <info>(eg: Wcli.Wconfig)</info>}
        {--f|force : Force the operation to run and ignore production warnings and confirmation questions.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'No description provided yet...';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $pluginCode = $this->getPluginIdentifier();
        $parts = explode('.', $pluginCode);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException("Invalid plugin name, either too many dots or not enough. Example: Author.PluginName");
        }
        $allPermissions = \BackendAuth::listTabbedPermissions();
        if (!$allPermissions) {
            return [];
        }
        $roles = \Backend\Models\UserRole::get();
        $roles = $roles->map(function ($role) use ($allPermissions) {
            return [
                'name' => $role->name,
                'description' => $role->description,
                'permissions' => $this->setPermissions($role->permissions, $allPermissions)
            ];
        });
        //trace_log($roles->toArray());
        $sourceFile = $this->getSourcePath() . '/stubs/roles.md.stub';
        //trace_log($sourceFile);
        $destinationFile = $this->getDestinationPath() . '/docs/all_roles.md';
        //trace_log($destinationFile);
        $destinationContent = $this->files->get($sourceFile);
        //trace_log(compact('roles'));
        $destinationContent = \Twig::parse($destinationContent, compact('roles'));
        $this->makeDirectory($destinationFile);

        $this->files->put($destinationFile, $destinationContent);
        $this->output->writeln('Création terminé');
    }

    public function setPermissions($rolePermissions, $allPermissions)
    {

        $permissions = [];
        foreach ($allPermissions as $plugin) {
            if (is_array($plugin)) {
                foreach ($plugin as  $permissionStd) {
                    if ($rolePermissions[$permissionStd->code] ?? false) {
                        if ($permissionStd->label) {
                            $permissions[$permissionStd->code] = \Lang::get($permissionStd->tab) . ': <b> ' . \Lang::get($permissionStd->label).'</b> ('.$permissionStd->code.')';
                        }
                    }
                }
            }
        }
        return $permissions;
    }
}
