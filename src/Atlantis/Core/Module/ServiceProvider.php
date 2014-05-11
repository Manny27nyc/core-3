<?php namespace Atlantis\Core\Module;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;


abstract class ServiceProvider extends BaseServiceProvider {
    protected $module_name;
    protected $title;
    protected $defer = false;

    /**
     *
     *
     * @return void
     */
    public function register(){
        if($module = $this->moduleGet( func_get_args() )){
            $this->moduleRegister($this->module_name);
        }
    }


    /**
     *
     *
     * @return void
     */
    public function boot(){
        if($module = $this->moduleGet( func_get_args() )){
            $this->moduleBoot($this->module_name);
        }
    }


    public function provides(){
        return ['modules'.$this->module_name];
    }


    public function info(){
        return [
            'title' => $this->title
        ];
    }

    /**
     *
     *
     * @return void
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
            $module_name = studly_case($module_name);
            $module_class = "Modules\\$module_name\\$module_name".'ServiceProvider';
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


    protected function modulePath(){
        return base_path().'/modules/'.$this->module_name;
    }
}