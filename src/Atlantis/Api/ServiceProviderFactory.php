<?php namespace Atlantis\Api;
 /**
 * Part of the mara-platform package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    mara-platform
 * @version    1.0.0
 * @author     Nematix LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 1997 - 2014, Nematix Corporation
 * @link       http://nematix.com
 */

use Atlantis\Core\Module\ServiceProviderFactory as BaseServiceProviderFactory;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;


abstract class ServiceProviderFactory extends BaseServiceProviderFactory{

    public function __construct($app){
        parent::__construct($app);

        $this->module_name = "{$this->name}.api";
    }


    public function register(){
        parent::register($this->module_name);
    }


    public function boot(){
        parent::boot($this->module_name);

        $this->bootRest();
        $this->bootRpc();
    }


    protected function modulePath(){
        $reflector = new \ReflectionClass(get_called_class());
        return dirname($reflector->getFileName()) . '/';
    }


    protected function bootRest(){
        $rests = $this->app['config']->get("modules.{$this->module_name}::api.rest");

        foreach($rests as $key => $rest){
            /** Get route name & option */
            $route_name = $rest['route_name'];
            $route_options = $this->app['config']->get("modules.{$this->module_name}::api.router.routes.$route_name.options");

            /** Create api mount-point */
            $this->app['router']->api(['version' => 'v1','prefix' => 'api'], function() use($key,$route_options){
                $this->app['router']->resource($route_options['route'],$key);
            });
        }
    }


    protected function bootRpc(){
        $rpcs = $this->app['config']->get("modules.{$this->module_name}::api.rpc");

        foreach($rpcs as $key => $rpc){
            /** Get route name & option */
            $route_name = $rpc['route_name'];
            $route_options = $this->app['config']->get("modules.{$this->module_name}::api.router.routes.$route_name.options");

            $this->app['router']->api(['version' => 'v1','prefix' => 'api', 'before'=>'api.rpc'], function() use($key,$route_options){
                $controller = $route_options['defaults']['controller'];
                $action = $route_options['defaults']['action'];

                $this->app['router']->post($route_options['route'].'/{'.$action.'?}',"{$controller}@actions");
            });
        }
    }

}