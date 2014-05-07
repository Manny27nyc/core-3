<?php namespace Atlantis\Core\Module\Facades;

use Illuminate\Support\Facades\Facade;


class Module extends Facade {

    /**
     * Name of the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'atlantis.module';
    }

}
