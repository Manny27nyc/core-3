<?php namespace Atlantis\Core\Config;

use Illuminate\Support\Facades\App;
use Illuminate\Config\FileLoader as BaseFileLoader;


class FileLoader extends BaseFileLoader {
    protected $configs;


    public function load($environment, $group, $namespace = null){
        $items = parent::load($environment, $group, $namespace);

        #i: Pre fetch Atlantis Core configs
        $this->configs = $this->getRequire(__DIR__ . '/../../../config/app.php');

        #i: If disable return loaded value
        if(!$this->configs['config']['enable']) return $items;

        #i: Get setting path
        $setting_path = $this->configs['config']['setting_path'];
        $file = "$setting_path/{$namespace}/{$group}.json";

        #i: Check for setting file exist
        if( $this->files->exists($file) ){
            #i: Load for settings
            $settings = json_decode(@file_get_contents($file),true) ?: array();

            #i: Merge settings with config
            $items = array_replace_recursive($items,$settings);
        }

        #i: Return json override config
        return $items;
    }
}