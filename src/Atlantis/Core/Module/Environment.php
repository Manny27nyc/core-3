<?php namespace Atlantis\Core\Module;

use Illuminate\Support\ClassLoader;


class Environment {
    protected $app;

    protected $repository;

    protected $modules = [];


    public function __construct($app){
        $this->app = $app;

        #i: Get default module repository name
        $repository_name = $app['config']->get('core::module.default','config');

        #i: Get module repository setting
        $this->repository = $repository = $app['config']->get('core::module.repositories.'.$repository_name);

        #i: Load modules from repository eloquent
        if( $repository_name == 'eloquent' ){
            #i: Get module array
            $modules = new $repository['model'];

            #i: Retrieve enabled modules
            $this->modules = $modules::where('enable',true)->get()->toArray();

        }else{
            $modules = (array)$app['config']->get($repository['group'].'::'.$repository['namespace'],[]);

            $this->modules = $this->object_to_array($modules);
        }
    }


    /**
     * Enabling module
     *
     * @return array
     */
    public function enable($module_name){
        #i: Get module info from available
        if( !isset($this->modules[$module_name]) ) $this->modules[$module_name] = $this->available()[$module_name];

        #i: Enabling the module
        $this->modules[$module_name]['enable'] = true;

        #i:
        $this->app['config']->set($this->repository['group'].'::'.$this->repository['namespace'].".$module_name",$this->modules[$module_name]);
    }


    /**
     * Disabling module
     *
     * @return array
     */
    public function disable($module_name){
        unset($this->modules[$module_name]);

        $this->app['config']->set($this->repository['group'].'::'.$this->repository['namespace'][$module_name],null);
    }


    public function has($module_name){
        return in_array($module_name, $this->available());
    }

    /**
     * Get enable module only
     *
     * @return array
     */
    public function all(){
        return $this->modules;
    }


    /**
     * Get module
     *
     * @param $module_name
     * @return array
     */
    public function get($module_name){
        return $this->modules[$module_name];
    }


    /**
     * Get modules path
     *
     * @param $module_name
     * @return array
     */
    public function getPath(){
        return base_path().'/modules';
    }


    /**
     * Get all available modules including enabled module
     *
     * @return array
     */
    public function available(){
        #i: Modules array & base path
        $modules = [];
        $module_base = $this->app['config']->get('core::module.base',base_path().'/modules');

        #i: Scanning folder for modules
        foreach( glob("$module_base/*",GLOB_ONLYDIR) as $dir ){
            #i: Get module name from directory
            $module_name = strtolower(basename($dir));

            #i: Prepare service provider name, foo_service_provider > FooServiceProvider
            $service_provider = studly_case($module_name . '_service_provider');

            #i: Check if the file exist
            if( $this->app['files']->exists( "$module_base/$module_name/$service_provider.php") ){
                if( !in_array($module_name, $this->modules) ){
                    $modules[$module_name] = [
                        'provider' => $service_provider,
                        'enable' => false
                    ];
                }
            }
        }

        #i: Merge with modules array
        $modules = array_merge($modules,$this->modules);

        return $modules;
    }


    /**
     * Register all enable module
     *
     * @return array
     */
    public function register(){
        foreach($this->all() as $name => $module){
            #i: Register service provider
            if( $module['enable'] ){
                #i: Provider class path
                $class_name = studly_case($name);
                $class_path = "Modules\\$class_name\\".$module['provider'];

                #i: Registering class
                $this->app->register($class_path);

                #i: Merging info from class
                $this->modules[$name] = array_merge_recursive($module,$this->app["modules.$name"]->info());
            };
        }
    }


    /**
     *
     * @return mixed
     */
    protected function moduleBase(){
        return $this->app['config']->get('core::module.base', base_path().'/modules');;
    }


    /**
     *
     *
     * @return array
     */
    protected function object_to_array($obj){
        $arrObj = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ($arrObj as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }
}