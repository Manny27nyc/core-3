<?php namespace Atlantis\Asset\Assetic;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection as BaseCollection;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\FileAsset;
use Assetic\Factory\AssetFactory;
use Atlantis\Asset\Assetic\GlobAsset;


abstract class AssetCollection extends BaseCollection {
    protected $files;
    protected $config;

    // Global vars
    public $base_path;
    public $build_path;
    public $mime;


    public function __construct(array $assets=[], array $filters=[], $base_path=''){
        #i: Default vars
        //$this->files = app('files');
        $this->config = app('config');

        #i: Default public vars
        $this->base_path = $base_path;
        $this->build_path = $this->config->get('core::asset.build_path');
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
        $asset = $this->applyTargetPath($asset);

        parent::add($asset);
    }


    /**
     * Override dump asset function
     *
     * @param   FilterInterface     $additionalFilter
     * @return  string
     */
    public function dump(FilterInterface $additionalFilter = null)
    {
        #i: Construct cache name
        $cache_name = app('atlantis.helpers')->string()->path_to_filename($this->base_path) . $this->mime;

        #i: Cache enable/disable
        if( !$this->config->get('core::asset.cached',true) ) app('cache')->forget($cache_name);

        #i: Check for modified files
        if( app('cache')->get($cache_name)['last_modified'] != $this->getLastModified() ) app('cache')->forget($cache_name);

        #i: Get cache content if enabled
        $output = app('cache')->rememberForever($cache_name, function() use($additionalFilter){
            // loop through leaves and dump each asset
            $parts = array();
            foreach ($this as $asset) {
                $asset = $this->filterComposer($asset);
                $parts[] = $asset->dump($additionalFilter);
            }

            return [
                'last_modified' => $this->getLastModified(),
                'data'          => implode("\n", $parts)
            ];
        });

        return $output['data'];
    }


    /**
     * Add asset by path
     *
     * @param   string    $asset_path
     * @param   bool      $return_object
     * @return  mixed
     */
    public function addByPath($asset_path, $return_object=false){
        #i: Get asset container
        $asset = $this->parseAsset($asset_path);

        #i: Add asset container to provider
        if(!$asset) return null;

        #i: Return object and don't add to collection
        if($return_object) return $asset;

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
     * Check for array if path asset is passed
     *
     * @param   array   $assets
     * @return  array
     */
    protected function processPathAssets(array $assets){
        foreach($assets as &$asset){
            if( is_string($asset) ) $asset = $this->addByPath($asset,true);
        }

        return $assets;
    }


    /**
     * Parse an asset path in object
     *
     * @param string    $asset_path
     * @return FileAsset|HttpAsset|GlobAsset|bool
     */
    protected function parseAsset($asset_path) {
        #i:::: Check for HttpAsset
        if (starts_with($asset_path, 'http://')) {
            return new HttpAsset($asset_path);

        #i:::: Check for GlobAsset
        }else if (str_contains($asset_path, array('*', '?'))) {
            return new GlobAsset($this->getAbsolutePath($asset_path));

        #i:::: Check for FileAsset
        }else {
            #i: Check for path
            $file_asset_path = $this->getAbsolutePath($asset_path, true);

            #i: Return if path exist
            if( $file_asset_path ) return new FileAsset($file_asset_path);
        }

        #i: Return asset
        return false;
    }


    /**
     * Apply target path from source path
     *
     * @param   AssetInterface  $asset
     * @return  mixed
     */
    protected function applyTargetPath($asset){
        #i: Set target path and return container
        $asset->setTargetPath($asset->getSourcePath());

        return $asset;
    }


    /**
     * Get absolute path
     *
     * @param string    $path
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


    /*    protected function assetAdd($asset_container){
        #i: Check for GlobAsset if yes then iterate the glob result
        if( get_class($asset_container) == 'Atlantis\Asset\Assetic\GlobAsset' ){
            foreach( $asset_container->all() as $asset_glob ){
                $this->assetAdd($asset_glob);
            }
            return true;
        }

        #i: Apply target path
        $asset_container = $this->applyTargetPath($asset_container);

        #i: Apply file filter
        $asset_container = $this->applyFilters($asset_container, $this->getFilters());

        #i: Add to AssetCollection
        $this->provider()->add($asset_container);

        return true;
    }*/



    /*protected function applyFilters($asset_container, $filters){
        $mimes = $this->config->get('core::asset.mimes.stylesheet');
        $filename = $asset_container->getTargetPath();

        #i: Apply filter based on extension
        foreach( $mimes as $filter ){
            if( str_contains($filename,$filter) ){
                $asset_container->setTargetPath( str_replace($filter, $mimes[0], $filename) );
                $asset_container->ensureFilter($this->filters->get($filter));
            }
        }

        #i: Apply filters
        foreach($filters as $filter){
            $asset_container->ensureFilter($this->filters->get($filter));
        }

        return $asset_container;
    }*/
}