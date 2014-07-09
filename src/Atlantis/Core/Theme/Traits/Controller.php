<?php namespace Atlantis\Core\Theme\Traits;


trait Controller {

    public function themeBoot($theme=null){
        if(!$theme) $theme = app('config')->get('core::app.theme.current');

        app('atlantis.theme')->load($theme);
    }

}