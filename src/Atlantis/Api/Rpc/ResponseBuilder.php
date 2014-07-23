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

use Illuminate\Http\Response as HttpResponse;
use Atlantis\Api\Rpc\Interfaces\ConfigInterface;
use Atlantis\Api\Rpc\Response as RpcResponse;
use Atlantis\Support\Exceptions\Provider as ExceptionProvider;


class ResponseBuilder {
    const SUCCESS_PROPERTY = 'result';
    const ERROR_PROPERTY = 'error';


    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }


    public function buildFromResult($request, $result)
    {
        if ($result instanceof RpcResponse) {
            $raw_result = json_decode($result->getContent());
            
        } else if ($result instanceof HttpResponse) {
            $raw_result = $result->getOriginalContent();
            
        } else {
            $raw_result = $result;
        }

        return $this->buildRaw($request, self::SUCCESS_PROPERTY, $raw_result);
    }


    public function buildFromException($request, \Exception $exception)
    {
        $handler = $this->config->getExceptionHandler();

        if ($handler) {
            return $handler($request, $exception);
        }

        if ($exception instanceOf ExceptionProvider) {
            return $this->buildFromJsonrpcException($request, $exception);

        } else {
            return $this->buildFromGenericException($request, $exception);
        }
    }


    private function buildFromGenericException($request, $exception)
    {
        $args = array(
            $request->getId(),
            -32603,
            'Internal Error',
            $exception->getMessage()
        );

        $body = \App::make('Atlantis\Support\Exceptions\Provider', $args);
        return $this->buildRaw($request, self::ERROR_PROPERTY, $body);
    }


    private function buildFromJsonrpcException($request, JsonrpcException $exception)
    {
        $args = array(
            $request->getId(),
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getError()
        );

        $body = \App::make('Atlantis\Support\Exceptions\Provider', $args);
        return $this->buildRaw($request, self::ERROR_PROPERTY, $body);
    }


    private function buildRaw($request, $output_field, $output_body)
    {
        $id = null;

        if ($request !== null) {
            if ($request->isNotification()) {
                return null;
            }

            $id = $request->getId();
        }

        return \App::make('Atlantis\Api\Rpc\Response', array($id, $output_field, $output_body));
    }

} 