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


class RequestBatch {
    protected $individual_requests;
    protected $responses = array();

    public function __construct(array $individual_requests = null)
    {
        if ($individual_requests !== null) {
            $this->setIndividualRequests($individual_requests);
        }
    }

    public function route()
    {
        $output = $this->responses;

        foreach ($this->individual_requests as $request) {

            $result = $request->route();

            if ( ! $request->isNotification()) {
                $output[] = $result;
            }
        }

        return $output;
    }

    public function setIndividualRequests(array $individual_requests)
    {
        foreach ($individual_requests as $individual_request) {
            $this->addIndividualRequest($individual_request);
        }
    }

    public function addIndividualRequest($individual_request)
    {
        $this->individual_requests[] = $individual_request;
    }

    public function addIndividualResponse(Response $response)
    {
        if ($response !== null) {
            $this->responses[] = $response;
        }
    }
} 