<?php namespace Atlantis\Core\Theme\Traits;

use Illuminate\Support\Facades\App;


trait Controller {

    public function themeBoot($theme=null){
        if(!$theme) $theme = App::make('config')->get('core::app.theme.default');

        App::make('atlantis.theme')->load($theme);
    }

}