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


abstract class ServiceProviderFactory extends BaseServiceProviderFactory{

    public function register(){
        parent::register($this->module_name);
    }


    public function boot(){
        parent::boot($this->module_name);

        $this->bootRest();
    }


    protected function modulePath(){
        $reflector = new \ReflectionClass(get_called_class());
        return dirname($reflector->getFileName()) . '/';
    }


    protected function bootRest(){
        $rests = $this->app['config']->get('modules.users.api::api.rest');

        foreach($rests as $key => $rest){
            $route_name = $rest['route_name'];
            $route_options = $this->app['config']->get("modules.users.api::api.router.routes.$route_name.options");

            $this->app['router']->api(['version' => 'v1','prefix' => 'api'], function() use($key,$route_options){
                $this->app['router']->resource($route_options['route'],$key);
            });
        }
    }

}