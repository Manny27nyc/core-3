<?php namespace Atlantis\Asset;

use Illuminate\Config\Repository;
use Illuminate\Http\Response;
use Atlantis\Asset\Config\AssetLoader;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCache;
use Assetic\Factory\AssetFactory;


class Environment {
    protected $files;
    protected $config;
    protected $helpers;
    protected $assets;

    protected $environment;
    protected $key;
    protected $processing;


    public function __construct($files,$config){
        #i: Vars
        $this->files = $files;
        $this->config = $config;
        $this->helpers = app('atlantis.helpers');
        $this->environment = $env = app()->environment();

        #i: Asset loader
        $loader = new AssetLoader();
        $this->assets = new Repository($loader, app('env'));
    }


    /**
     * Register stylesheet into collection array
     *
     * @param string    $namespace
     * @param array     $assets
     */
    public function register($namespace,array $assets=[]){

    }


    public function extend($namespace,array $assets=[]){
        $this->assets->getLoader()->addNamespace($namespace,$assets);
    }


    /**
     * Getting repo value
     *
     * @param $key
     * @return $this
     */
    public function get($key){
        #i: Check for wildcard key
        if( starts_with($key,['*::','::']) ){
            $key = str_replace(['*::','::'],'',$key);
        };

        #i: Prepare the assets
        $this->processing = $this->assets->get($this->key = $key);

        #i: Immediate return on empty
        if( empty($this->processing) ) return $this;

        #i: Configure target path for collection group
        $file_extension = $this->config->get('core::asset.mimes')[$this->processing->mime][0];
        list($namespace,$group) = $this->assets->parseKey($this->key);

        if(empty($namespace)) $namespace = $group;

        #i: Set target path
        $this->processing->setTargetPath("$namespace.$file_extension");

        return $this;
    }


    /**
     * Setting repo value
     *
     * @param $key
     * @param $value
     */
    public function set($key,$value){
        $this->assets->set($key,$value);
    }


    public function fetch($path){
        // Fetch direct from path support glob
    }


    /**
     * Stream assets to client
     */
    public function stream(){
        if( empty($this->processing) ) echo null;

        #i: Create response base on mime and assets item
        $response = \Response::make($this->processing->dump(), 200);
        $response->header('Content-Type', 'text/'.$this->processing->mime);

        echo $response;
    }


    /**
     * Static assets
     *
     * @param array $assets
     * @return array
     */
    public function html($assets=[]){
        #i: Vars
        $assets = empty($assets) ? $this->processing : $assets;

        if( empty($this->processing) ) return null;

        $mime = $this->processing->mime;

        #i: write file to disk
        $asset_files = $this->save($assets);

        #i: Create html
        array_walk($asset_files, function(&$asset) use($mime){
            if( $mime == 'javascript' ){
                $asset = '<script href="'.$asset.'" type="text/'.$mime.'"></script>';
            }else{
                $asset = '<link href="'.$asset.'" rel="'.$mime.'" type="text/'.$mime.'" />';
            }
        });

        echo implode("\n",$asset_files);
    }


    /**
     * Save assets to disk
     *
     * @param array $assets
     * @return array
     */
    public function save($assets=[]){
        #i: Assets assignment
        $assets = empty($assets) ? $this->items() : $assets;

        if( empty($this->processing) ) return [];

        #i: Get build & relative path
        $build_path = $this->config->get('core::asset.build_path');
        $relative_path = $this->helpers->string->relative_path(public_path(),$build_path);

        #i: Check for production environment
        $is_production = in_array($this->environment, $this->config->get('core::asset.cache.environment'));

        #i: Get asset writer
        $writer = new AssetWriter($build_path);

        #i: Write to disk, production will create single file and cached
        if( $is_production ){
            $writer->writeAsset($assets);
            return [app('url')->asset($relative_path.$this->processing->getTargetPath())];

        }else{
            $asset_files = [];

            foreach( $assets->dump() as $asset ){
                $asset_files[] = app('url')->asset($relative_path.$asset->getTargetPath());
                $writer->writeAsset($asset);
            }

            return $asset_files;
        }
    }

}