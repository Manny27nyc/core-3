<?php namespace Atlantis\Asset\Assetic;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection as BaseCollection;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\FileAsset;
use Atlantis\Asset\Assetic\GlobAsset;


abstract class AssetCollection extends BaseCollection {
    protected $files;
    protected $config;
    protected $content;
    protected $environment;
    protected $mimes;

    // Global vars
    public $base_path;
    public $build_path;
    public $mime;


    public function __construct(array $assets=[], array $filters=[], $base_path=''){
        #i: Default vars
        $this->files = app('files');
        $this->config = app('config');
        $this->environment = $env = app()->environment();

        #i: Default public vars
        $this->base_path = $base_path;
        $this->build_path = $this->config->get('core::asset.build_path');
        $this->mimes = $this->config->get('core::asset.mimes');
        $this->mime = strtolower(class_basename($this));

        #i: Check for path asset in array
        $assets = $this->processPathAssets($assets);

        #i: Parent
        parent::__construct($assets,$filters,$base_path);
    }


    /**
     * Override add asset function
     *
     * @param AssetInterface $asset
     */
    public function add(AssetInterface $asset)
    {
        #i: Never add asset as GlobAsset
        if( get_class($asset) == 'Atlantis\Asset\Assetic\GlobAsset' ){
            foreach($asset->all() as $glob_asset){
                $this->add($glob_asset);
            };

            return;
        };

        #i: Applying custom filter
        $asset = $this->filterComposer($asset);

        #i: Applying target path
        $asset = $this->applyTargetPath($asset);

        parent::add($asset);
    }


    /**
     * Override dump asset function
     *
     * @param   FilterInterface     $additionalFilter
     * @return  mixed
     */
    public function dump(FilterInterface $additionalFilter = null)
    {
        #i: Construct cache name
        $cache_name = app('atlantis.helpers')->string()->path_to_filename($this->base_path) . "-{$this->mime}";

        if( in_array($this->environment, $this->config->get('core::asset.cache.environment')) ) {
            #i: Rebuild asset collection with cache
            $output = $this->getCache($cache_name,$additionalFilter);

            return $output['data'];

        }else{
            #i: Clear previous cache
            app('cache')->forget($cache_name);

            #i: Build asset collection
            return $this->build($additionalFilter,false);
        }


    }


    public function getCache($cache_name, $additionalFilter=null){
        #i: Check for modified files
        if( app('cache')->get($cache_name)['last_modified'] != $this->getLastModified() ) app('cache')->forget($cache_name);

        #i: Get cache content if enabled
        return app('cache')->rememberForever($cache_name, function() use($additionalFilter){
            #i: Build if cache is new
            $parts = $this->build($additionalFilter);

            #i: Return concatenated content, always in cache
            return [
                'last_modified' => $this->getLastModified(),
                'data'          => implode("\n", $parts)
            ];
        });
    }


    /**
     * Build & prepare collection
     *
     * @param null $additionalFilter
     * @param bool $dump
     * @return array
     */
    protected function build($additionalFilter=null, $dump=true){
        $parts = array();

        foreach ($this as $asset) {
            #i: Target path
            $asset = $this->applyTargetPath($asset);

            #i: Dump content for return
            if($dump) $asset = $asset->dump($additionalFilter);

            $parts[] = $asset;
        }

        return $parts;
    }

    /**
     * Check for array if path asset is passed
     *
     * @param   array   $assets
     * @return  array
     */
    protected function processPathAssets($assets){
        array_walk($assets, function(&$asset){
            if( is_string($asset) ) {
                $asset = $this->parseAsset($asset);
            }
        });

        return $assets;
    }


    /**
     * Add asset by path
     *
     * @param   string    $asset_path
     * @return  mixed
     */
    public function addByPath($asset_path){
        #i: Get asset container
        $asset = $this->parseAsset($asset_path);

        #i: Add object to collection
        $this->add($asset);
    }


    /**
     * Overridable filter composer
     *
     * @param   AssetInterface  $asset
     * @return  AssetInterface
     */
    protected function filterComposer($asset){
        return $asset;
    }


    /**
     * Parse an asset path in object
     *
     * @param string    $asset_path
     * @return FileAsset|HttpAsset|GlobAsset|bool
     */
    protected function parseAsset($asset_path) {
        $absolute_path = $this->getAbsolutePath($asset_path);

        #i:::: Check for HttpAsset
        if (starts_with($asset_path, 'http://')) {
            return new HttpAsset($asset_path);

        #i:::: Check for GlobAsset
        }else if (str_contains($asset_path, array('*', '?'))) {
            return new GlobAsset($absolute_path);

        #i:::: Check for FileAsset
        }else if( $this->getAbsolutePath($asset_path, true) ){
            #i: Return if path exist
            return new FileAsset($absolute_path);

        }else{
            return null;
        }
    }


    /**
     * Apply target path from source path
     *
     * @param   AssetInterface  $asset
     * @return  mixed
     */
    protected function applyTargetPath($asset){
        #i: Set target path and return container
        $file_ext = $this->files->extension($asset->getSourcePath());
        $default_ext = $this->mimes[$this->mime][0];

        #i: Construct filename with proper extension
        $file_source_path = str_replace($file_ext,$default_ext,$asset->getSourcePath());

        #i: Final file path structure
        $asset->setTargetPath($this->mime . '/' .$file_source_path);

        #i: Cache busting
        $asset = $this->cacheBustingAsset($asset);

        return $asset;
    }


    protected function cacheBustingAsset($asset){
        $source_path = $asset->getTargetPath();

        if( !$extension = pathinfo($source_path,PATHINFO_EXTENSION)){
            return $asset;
        }

        #i: Getting hash value of file by last modified
        $hash = hash_init('md5');
        hash_update($hash,$asset->getLastModified());
        $file_hash = hash_final($hash);

        #i: Construct cached path from source path
        $cached_path = "-$file_hash.$extension";
        $source_path = preg_replace('/\.'.$extension.'$/',$cached_path,$source_path);

        #i: Apply the cached path
        $asset->setTargetPath($source_path);

        return $asset;
    }

    /**
     * Get absolute path
     *
     * @param   string  $path
     * @param  bool    $real_path
     * @return string
     */
    protected function getAbsolutePath($path,$real_path=false) {
        $base_path = empty($this->base_path) ? base_path() : $this->base_path;

        #i: Already absolute if path starts with / or drive letter
        if (preg_match(',^([a-zA-Z]:|/),', $path)) return $path;

        #i: Check for real path
        if($real_path) return realpath("$base_path/$path");

        #i: Return base path
        return "$base_path/$path";
    }
}