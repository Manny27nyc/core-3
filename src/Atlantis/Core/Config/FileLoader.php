<?php namespace Atlantis\Core\Config;

use Illuminate\Support\Facades\App;
use Illuminate\Config\FileLoader as BaseFileLoader;


class FileLoader extends BaseFileLoader {

    public function load($environment, $group, $namespace = null){
        $items = array();

        $path = $this->getPath($namespace);

        if (is_null($path)){
            return $items;
        }

        #i: Main config file
        $file = "{$path}/{$group}.php";
        if ($this->files->exists($file)){
            $items = $this->files->getRequire($file);
        }

        #i: Environment specific
        $file = "{$path}/{$environment}/{$group}.php";
        if ($this->files->exists($file)){
            $items = $this->mergeEnvironment($items, $file);
        }

        #i: Pre fetch Atlantis Core configs
        $this->core_config = $this->getRequire(__DIR__ . '/../../../config/app.php');

        #i: Get setting path
        $setting_path = $this->core_config['config']['setting_path'];
        $file = "$setting_path/{$namespace}/{$group}.json";

        #i: Check for setting file exist
        if( $this->files->exists($file) ){
            #i: Load for settings
            $settings = json_decode(@file_get_contents($file)) ?: array();

            #i: Merge settings with config
            $items = array_replace_recursive($items,(array)$settings);
        }

        return $items;
    }
}