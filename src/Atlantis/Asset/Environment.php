<?php

namespace Atlantis\Asset;

use Illuminate\Config\Repository;
use Illuminate\Http\Response;
use Atlantis\Asset\Config\AssetLoader;
use Assetic\AssetWriter;


class Environment {
    /** @var $files Array Files collection */
    protected $files;

    /** @var $config mixed Config object */
    protected $config;

    /** @var $helpers \Atlantis\Helpers\Facades\Helpers Helpers instance */
    protected $helpers;

    /** @var $assets \Illuminate\Config\Repository Repository instance */
    protected $assets;

    /** @var $environment mixed Environment instance */
    protected $environment;

    /** @var $key String Current asset key */
    protected $key;

    /** @var $processing \Illuminate\Config\Repository Current asset repository */
    protected $processing;


    /**
     * Constructor
     *
     * @param $files
     * @param $config
     */
    public function __construct($files,$config){
        /** Vars */
        $this->files = $files;
        $this->config = $config;
        $this->helpers = app('atlantis.helpers');
        $this->environment = $env = app()->environment();

        /** Atlantis Asset loader */
        $loader = new AssetLoader();
        $this->assets = new Repository($loader, app('env'));
    }


    /**
     * Extend assets into collection array
     *
     * @param string    $namespace
     * @param array     $assets
     */
    public function extend($namespace,array $assets=[]){
        // If existing namespace given, the previous assets on namespace will be override.
        // Namespace with name of `foo` can be called `app('atlantis.asset')->get('foo')->html()`
        // or `app('atlantis.asset')->get('foo::javascript')->html()` instead for javascript only
        $this->assets->getLoader()->addNamespace($namespace,$assets);
    }


    /**
     * Compiled and return assets list
     *
     * @param $key
     * @return $this
     */
    public function get($key){
        /** Check for wildcard key */
        if( starts_with($key,['*::','::']) ){
            $key = str_replace(['*::','::'],'',$key);
        };

        /** Prepare the assets */
        $this->processing = $this->assets->get($this->key = $key);

        /** Immediate return on empty */
        if( empty($this->processing) ) return $this;

        /** Configure target path for collection group */
        $file_extension = $this->config->get('core::asset.mimes')[$this->processing->mime][0];
        list($namespace,$group) = $this->assets->parseKey($this->key);

        /** If namespace empty use group name */
        if(empty($namespace)) $namespace = $group;

        /** Set target path */
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
     *
     */
    public function stream(){
        if( empty($this->processing) ) echo null;

        /** Create response base on mime and assets item */
        $response = \Response::make($this->processing->dump(), 200);
        $response->header('Content-Type', 'text/'.$this->processing->mime);

        echo $response;
    }


    /**
     * Static assets in HTML
     *
     * @param array $assets
     * @return array
     */
    public function html($assets=[]){
        /** Vars */
        $assets = empty($assets) ? $this->processing : $assets;

        /** Immediate return on empty */
        if( empty($this->processing) ) return null;

        $mime = $this->processing->mime;

        /** write file to disk */
        $asset_files = $this->save($assets);

        /** Create html tag */
        array_walk($asset_files, function(&$asset) use($mime){
            if( $mime == 'javascript' ){
                $asset = '<script src="'.$asset.'"></script>';
            }else{
                $asset = '<link href="'.$asset.'" rel="'.$mime.'" type="text/css" />';
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
        /** Assets assignment */
        $assets = empty($assets) ? $this->items() : $assets;

        if( empty($this->processing) ) return [];

        /** Get build & relative path */
        $build_path = $this->config->get('core::asset.build_path');
        $relative_path = $this->helpers->string->relative_path(public_path(),$build_path);

        /** Check for production environment */
        $is_production = in_array($this->environment, $this->config->get('core::asset.cache.environment'));

        /** Get asset writer */
        $writer = new AssetWriter($build_path);

        /** Write to disk, production will create single file and cached */
        if( $is_production ){
            $writer->writeAsset($assets);
            return [app('url')->asset($relative_path.$this->processing->getTargetPath())];

        }else{
            $asset_files = [];

            foreach( $assets->dump() as $asset ){
                /** Create assets url collection */
                $asset_files[] = app('url')->asset($relative_path.$asset->getTargetPath());

                /** If file exist skip write */
                if( $this->files->exists($build_path.'/'.$asset->getTargetPath()) ) continue;

                /** Write to disk */
                $writer->writeAsset($asset);
            }

            return $asset_files;
        }
    }

}