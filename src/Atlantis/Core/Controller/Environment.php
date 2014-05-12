<?php namespace Atlantis\Core\Controller;
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


class Environment {
    protected $extensions = [];
    protected $attributes = [];


    /**
     *
     *
     * @return void
     */
    public function extend($name, $value){
        if( class_basename($value) == 'Closure' ){
            $this->extensions[$name] = $value;
        }else{
            $this->attributes[$name] = $value;
        }
    }


    /**
     *
     *
     * @return mixed
     */
    public function extension($name){
        if( isset($this->extensions[$name]) ) return $this->extensions[$name];

        return null;
    }


    /**
     *
     *
     * @return mixed
     */
    public function attribute($name){
        if( isset($this->attributes[$name]) ) return $this->attributes[$name];

        return null;
    }
}