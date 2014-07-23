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

use  Atlantis\Api\Rpc\Exceptions\InternalErrorException;

class RequestValidator
{
    public function validate(array $request)
    {
        if ( ! $this->isValidRequest($request)) {
            throw new InternalErrorException();
        }
    }

    public function isValidRequest(array $request)
    {
        return $this->hasValidVersion($request) &&
        $this->hasValidMethodFormat($request) &&
        $this->hasValidParameters($request);
    }

    public function hasValidVersion(array $request)
    {
        return isset($request['jsonrpc']) && $request['jsonrpc'] === '2.0';
    }

    public function hasValidMethodFormat(array $request)
    {
        return isset($request['method']);
    }

    public function hasValidParameters(array $request)
    {
        return !array_key_exists('params', $request) ||
            is_array($request['params']) ||
            is_object($request['params']) ||
            $request['params'] === null;
    }
}