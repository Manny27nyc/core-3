<?php
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

namespace Atlantis\Api\Rpc;

use Atlantis\Api\Rpc\Interfaces\RequestInterface;


class Request implements RequestInterface{
    protected $id;
    protected $version;
    protected $method;
    protected $params;

    public function __construct($request_data)
    {
        $this->setMethod($request_data['method']);
        $this->setVersion($request_data['jsonrpc']);

        if (array_key_exists('id', $request_data)) {
            $this->setId($request_data['id']);
        }

        if (array_key_exists('params', $request_data)) {
            $this->setParams($request_data['params']);
        }
    }

    public function data($property_name)
    {
        if ( ! isset($this->params[$property_name])) {
            return null;
        }

        return $this->params[$property_name];
    }

    public function rawData()
    {
        return $this->params;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function isNotification()
    {
        return $this->id === null;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function setParams($params)
    {
        if ($params === null) {
            $this->params = array();
        } else if (is_object($params)) {
            $this->params = (array)$params;
        } else {
            $this->params = $params;
        }
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }
} 