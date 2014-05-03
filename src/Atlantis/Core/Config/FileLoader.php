<?php namespace Atlantis\Core\Config;

use Illuminate\Support\Facades\App;
use Illuminate\Config\FileLoader as BaseFileLoader;
use Illuminate\Filesystem\Filesystem;


class FileLoader extends BaseFileLoader {
    protected $core_config;


    public function __construct(Filesystem $files, $defaultPath){
        parent::__construct($files,$defaultPath);

        #i: Pre fetch Atlantis Core configs
        $this->core_config = App::make('config')->get('core::app');
    }


    public function load($environment, $group, $namespace = null){
        $configs = parent::load($environment, $group, $namespace);

        #i: Get setting path
        $setting_path = $this->core_config['config']['setting_path'] . "/$namespace/";
        $file_path = $setting_path . $group . '.json';

        #i: Check for setting file exist
        if( \File::exists($file_path) ){
            #i: Load for settings
            $settings = json_decode(@file_get_contents($file_path)) ?: array();

            #i: Merge settings with config
            $configs = array_merge($configs,(array)$settings);
        }

        return $configs;
    }

}