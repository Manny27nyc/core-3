<?php namespace Atlantis\API;

class Environment {

    public function __construct()
    {

    }


    public function get($name)
    {
        return app('dingo.api.dispatcher')->get($name);
    }
} 