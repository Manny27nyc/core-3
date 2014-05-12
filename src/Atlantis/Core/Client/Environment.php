<?php namespace Atlantis\Core\Client;

use Atlantis\Core\Client\Javascript\Provider as JavascriptProvider;

class Environment {
    protected $javascript;


    public function __construct(JavascriptProvider $javascript){
        $this->javascript = $javascript;
    }


    public function javascript(){
        return $this->javascript;
    }

}