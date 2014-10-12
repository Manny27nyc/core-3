<?php namespace Atlantis\Core\Module;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;


/**
 * Class ServiceProviderFactory
 * @package Atlantis\Core\Module
 *
 * Getting Config :
 *
 * var $config = $this->app['config']->get('modules.users::users');
 *
 */
abstract class ServiceProviderFactory extends BaseServiceProvider {
    protected $module_name;
    protected $title;
    protected $defer = false;

    /**
     *
     *
     * @return void
     */
    public function register(){
        if( $this->moduleGet(func_get_args()) ){
            $this->moduleRegister($this->module_name);
        }
    }


    /**
     *
     *
     * @return void
     */
    public function boot(){
        if( $this->moduleGet( func_get_args()) ){
            $this->moduleBoot($this->module_name);
        }
    }


    /**
     * @return array
     */
    public function provides(){
        return ['modules'.$this->module_name];
    }


    /**
     * @return array
     */
    public function info(){
        return [
            'title' => $this->title
        ];
    }


    protected function modulePath(){
        $module_base  = $this->app['config']->get('core::module.base');

        return $module_base.$this->module_name;
    }


    /**
     *
     * @param Array
     * @return string
     */
    protected function moduleGet($args){
        $module = (isset($args[0]) and is_string($args[0])) ? $args[0] : null;
        $this->module_name = $module;

        return $module;
    }


    /**
     *
     *
     * @return void
     */
    protected function moduleRegister($module_name){
        #i: Registering package
        $this->package("modules/$module_name", "modules.$module_name", $this->modulePath());

        $this->app->bind("modules.$module_name", function($app) use($module_name){
            //@info Sanitize dot for path $ class name
            $module_path = implode('\\', array_map('ucwords',explode('.',$module_name)));
            $module_name = studly_case( str_replace('.','_',$module_name) );

            //$module_name = studly_case($module_name);
            $module_class = "Modules\\$module_path\\$module_name".'ServiceProvider';
            return new $module_class($app);
        });
    }


    /**
     *
     *
     * @return void
     */
    protected function moduleBoot($module_name){
        \ClassLoader::addDirectories(array(
            $this->modulePath().'/controllers',
            $this->modulePath().'/models',
            $this->modulePath().'/commands',
            $this->modulePath().'/database/seeds',
            $this->modulePath().'/api',
        ));

        #i: Registering module config, view and lang
        $this->app['config']->package("modules/$module_name", $this->modulePath().'/config');
        $this->app['view']->addNamespace($module_name, $this->modulePath().'/views');
        $this->app['translator']->addNamespace($module_name, $this->modulePath().'/lang');


        #i: Registering module filters
        $filters = $this->modulePath().'/filters.php';
        if (file_exists($filters)) require $filters;

        #i: Registering module filters
        $events = $this->modulePath().'/events.php';
        if (file_exists($events)) require $events;

        #i: Registering module route
        $routes = $this->modulePath().'/routes.php';
        if (file_exists($routes)) require $routes;
    }

}