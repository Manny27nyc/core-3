<?php namespace Atlantis\Asset;
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


class ServiceProvider extends BaseServiceProvider {

    /**
     * Registering Service Provider
     * @return void
     */
    public function register(){
        $this->registerBladeExtension();
        $this->registerServiceAsset();
    }


    /**
     * Registering Service Asset
     */
    public function registerServiceAsset(){
        $this->app['atlantis.asset'] = $this->app->share(function($app){
            return new Environment($app['files'],$app['config']);
        });
    }

    /**
     * Registering Blade Extension
     * @return void
     */
    public function registerBladeExtension(){
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->extend(function($value, $compiler){
            $matcher = "/(?<!\\w)(\\s*)@javascripts(\\(\\')(\\s*.*)(\\'\\))/";

            return preg_replace($matcher, '$1<?php echo app(\'atlantis.asset\')->get(\'$3::javascript\')->html(); ?>', $value);
        });

        $blade->extend(function($value, $compiler){
            $matcher = "/(?<!\\w)(\\s*)@stylesheets(\\(\\')(\\s*.*)(\\'\\))/";

            return preg_replace($matcher, '$1<?php echo app(\'atlantis.asset\')->get(\'$3::stylesheet\')->html(); ?>', $value);
        });

        $blade->extend(function($value, $compiler){
            $matcher = $compiler->createMatcher('assets');
            return preg_replace($matcher, '$1<?php echo app(\'atlantis.asset\')->get$2->html(); ?>', $value);
        });
    }

    /**
     * SP Provides
     * @return array
     */
    public function provides(){
        return ['atlantis.asset'];
    }

}