<?php namespace Atlantis\Support\Exceptions;

use Exception;

abstract class Provider extends Exception {
    protected $data;

    /**
     * @param string $message
     * @param int $code
     * @param null $data
     */
    public function __construct($message, $code, $data = null)
    {
        parent::__construct($this->message, $this->code);
        $this->data = $data;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }
} 