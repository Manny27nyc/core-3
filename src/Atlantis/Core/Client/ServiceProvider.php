<?php namespace Atlantis\Core\Client;
/**
 * Part of the Atlantis package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Atlantis
 * @version    1.0.0
 * @author     Nematix LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 1997 - 2013, Nematix LLC
 * @link       http://nematix.com
 */

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Atlantis\Core\View;


class ServiceProvider extends BaseServiceProvider {

    /**
     * Registering Service Provider
     * @return void
     */
    public function register(){
        $this->app['atlantis.client.javascript'] = $this->app->share(function($app){
            #i: Get configs
            $view = $app['config']->get('core::client.javascript.bind');
            $namespace = $app['config']->get('core::client.javascript.namespace');

            #i: Get view binder
            $binder = new View\Binder($app['events'],$view);

            #i: Return provider instance
            return new Javascript\Provider($binder,$namespace);
        });

        $this->app['atlantis.client'] = $this->app->share(function($app){
            $javascript = $app['atlantis.client.javascript'];

            return new Environment($javascript);
        });
    }


    /**
     * SP Provides
     * @return array
     */
    public function provides(){
        return ['atlantis.client'];
    }

}