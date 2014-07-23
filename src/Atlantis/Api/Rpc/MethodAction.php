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

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request as HttpRequest;
use Atlantis\Api\Rpc\Interfaces\ConfigInterface;
use Atlantis\Api\Rpc\Interfaces\RequestInterface;
use Atlantis\Api\Rpc\Interfaces\ResponseBuilderInterface;


class MethodAction {
    protected $resolver;
    protected $request;


    public function __construct(ConfigInterface $config,
                                ResponseBuilderInterface $response_builder,
                                RequestInterface $request)
    {
        $this->resolver = new MethodResolver($config);
        $this->request = $request;
        $this->response_builder = $response_builder;
    }


    public function invoke()
    {
        try {
            HttpRequest::replace($this->request->rawData());

            //@info Resolve request to method
            $callable = $this->resolver->resolve($this->request->getMethod());

            //@info Execute method
            $result = $this->executeRequest($callable);

            //@info Build response from result and set event
            $response = $this->response_builder->buildFromResult($this->request, $result);
            $this->fireBeforeOutputEvent($response, $callable);

            //@info Return response
            return $response;

        } catch (\Exception $e) {
            return $this->response_builder->buildFromException($this->request, $e);
        }
    }

    public function isNotification()
    {
        return false;
    }


    protected function executeRequest(array $callable)
    {
        $this->fireBeforeExecutionEvent($callable);
        return call_user_func($callable);
    }


    protected function fireBeforeExecutionEvent($callable)
    {
        Event::fire('jsonrpc.beforeExecution', array($callable[0], $callable[1]));
    }


    protected function fireBeforeOutputEvent($response, $callable)
    {
        Event::fire('jsonrpc.beforeOutput', array($response, $callable[0], $callable[1]));
    }
} 