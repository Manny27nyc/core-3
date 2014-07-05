<?php namespace Atlantis\Helpers;


class Arrays {

    public function dimensional_from_path($path){
        $path = str_replace('\\','/',$path);
        $parts = explode('/',$path);
        $arr = array();

        while ($bottom = array_pop($parts)) {
            $arr = array($bottom => $arr);
        }

        return $arr;
    }

}