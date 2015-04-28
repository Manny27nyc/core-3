<?php

namespace Atlantis\Api\Controller;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Dingo\Api\Routing\Controller;


class BaseController extends Controller{

    protected $result = ["result" => null,"error" => null, "id" => null];

    public function actions(){
        $post = Input::all();
        $this->result['id'] = $post['id'];

        try{
            if( empty($post['method']) ) throw new \Exception('No method provided!');

            $method_name = 'method'.studly_case($post['method']);

            if( method_exists($this, $method_name) ){
                return $this->{$method_name}();

            } else{
                throw new \Exception('Method not allowed!');
            }

        } catch(Exception $e){
            $this->result['error'] = $e->getMessage();

        }

        return Response::json($this->result);
    }

}