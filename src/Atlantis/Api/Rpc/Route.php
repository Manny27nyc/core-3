<?php

namespace Atlantis\Api\Rpc;

use Exception;
use Illuminate\Support\Facades\Input;
use Atlantis\Api\Rpc\Interfaces\RouteInterface;
use Atlantis\Api\Rpc\Interfaces\RequestValidatorInterface;
use Atlantis\Api\Rpc\Interfaces\ResponseBuilderInterface;
use Atlantis\Api\Rpc\Exceptions\ParseErrorException;
use Atlantis\Api\Rpc\Exceptions\InvalidRequestException;


class Route implements RouteInterface{

    public function __construct(RequestValidatorInterface $validator, ResponseBuilderInterface $builder)
    {
        $this->validator = $validator;
        $this->builder = $builder;
    }

    public function route()
    {
        try {
            $raw_request = $this->getRawRequest();
            $method = $this->interpretRawRequest($raw_request);
            $result = $method->invoke();

        } catch (\Exception $e) {
            $result = $this->builder->buildFromException(null, $e);
        }

        $this->outputResult($result);
    }

    protected function getRawRequest()
    {
        $json = Input::json()->all();

        if (sizeof($json) == 0) {
            throw new ParseErrorException();
        }

        return $json;
    }

    protected function outputResult($result)
    {
        if (is_array($result) || is_object($result)) {
            echo json_encode($result);
        } else {
            echo $result;
        }
    }

    public function interpretRawRequest(array $raw_request)
    {
        if ($this->isBatchRequest($raw_request)) {
            return $this->interpretBatchRequest($raw_request);
        } else {
            return $this->interpretSingleRequest($raw_request);
        }

    }


    public function interpretSingleRequest(array $raw_single_request)
    {
        $this->validator->validate($raw_single_request);
        $request = \App::make('Atlantis\Api\Rpc\Interfaces\RequestInterface', array($raw_single_request));

        if (!$request->isNotification()) {
            $method = \App::make('Atlantis\Api\Rpc\MethodAction', array($request));

        } else {
            $method = \App::make('Atlantis\Api\Rpc\MethodNotification', array($request));
        }
        return $method;
    }


    public function interpretBatchRequest(array $raw_batch_request)
    {
        $batch = new RequestBatch();

        foreach ($raw_batch_request as $raw_request) {
            try {
                $request = $this->interpretSingleRequest($raw_request);
                $batch->addIndividualRequest($request);

            } catch (Exception $e) {
                $response = $this->builder->buildFromException(null, $e);
                $batch->addIndividualResponse($response);
            }
        }

        return $batch;
    }


    /**
     * Check if request is batch mode
     *
     * @param $raw_request
     * @return bool
     * @throws Exceptions\InvalidRequestException
     */
    public function isBatchRequest($raw_request)
    {
        if (!is_array($raw_request)) {
            throw new InvalidRequestException();
        }

        if (isset($raw_request['jsonrpc'])) {
            return false;
        }

        if (isset($raw_request[0]) && isset($raw_request[0]['jsonrpc'])) {
            return true;
        }

        throw new InvalidRequestException();
    }

} 