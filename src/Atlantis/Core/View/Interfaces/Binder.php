<?php namespace Atlantis\Core\View\Interfaces;


interface Binder {

    /**
     * Bind the JavaScript to the view
     *
     * @param $js
     */
    public function javascript($js,$header);

}