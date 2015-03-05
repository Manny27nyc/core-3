<?php namespace Atlantis\Helpers;

use Illuminate\Support\Facades\Log;
use FirePHP;

class Debug {

    /**
     * Log
     *
     * @param $mixed
     * @param string $type
     * @param bool $console
     */
    public function log($mixed,$type='debug',$console=true){
        if( !app('config')->get('app.debug') ) return;

        $accept_type = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert'];

        /** @var $type String Sanitized log type $type */
        $type = str_contains($type,$accept_type) ? $type : 'debug';

        Log::$type($mixed);

        /** Log to Firebug console */
        if($console) $this->logFirephp($mixed,$type);
    }


    /**
     * Log to Firephp
     *
     * @param $mixed
     * @param $type
     */
    protected function logFirephp($mixed,$type){
        /** @var $logger Firephp handler */
        $logger = FirePHP::getInstance(true);

        $options = array('maxObjectDepth' => 2,
            'maxArrayDepth' => 5,
            'maxDepth' => 10,
            'useNativeJsonEncode' => true,
            'includeLineNumbers' => true);
        $logger->setOptions($options);

        $logger->log($mixed,studly_case($type));
    }

}