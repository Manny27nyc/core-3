<?php namespace Atlantis\Helpers\Facades;

use Illuminate\Support\Facades\Facade;


class Helpers extends Facade {

    protected static function getFacadeAccessor(){
        return 'atlantis.helpers';
    }

}