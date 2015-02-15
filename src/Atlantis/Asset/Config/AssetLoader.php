<?php namespace Atlantis\Asset\Config;

use Illuminate\Support\Facades\App;
use Illuminate\Config\LoaderInterface;


class AssetLoader implements LoaderInterface{
    /** @var array Assets collection hints */
    protected $hints = [];

    /** @var array Assets collection */
    protected $assets = [];


    public function __construct()
    {
        #i: Get common assets and prefixes
        $assets = app('config')->get('core::asset.assets');
        $mimes = app('config')->get('core::asset.mimes');
        $prefixes = app('config')->get('core::asset.register.prefixes');

        #i: If no common asset configured then skip
        if( empty($assets) ) return;

        #i: Apply prefixes
        $assets = app('atlantis.helpers')->string()->applyPrefixes($assets,$prefixes);

        #i: Get and construct common asset types
        $asset_types = array_keys($mimes);
        foreach( $asset_types as $asset_type){
            #i: Create an AssetCollection of current asset
            if( isset($assets[$asset_type]) ) $this->addNamespace('common',$assets);
        }
    }


    /**
     * Load the given configuration group.
     *
     * @param  string $environment
     * @param  string $group
     * @param  string $namespace
     * @return array
     */
    public function load($environment, $group, $namespace = null)
    {
        #i: If no namespace provide set default
        $key = $this->getCollection($group,$namespace);

        #i: Check for existing value
        if( isset($this->assets[$key]) ){
            return $this->assets[$key];
        }

        #i: Check and get assets existed in hints array
        $assets = $this->exists($group,$namespace);

        #i: If library not exist then return empty
        if( !$assets ) return [];

        #i: Get assets array
        $assets_common = $this->parseAssetsArray($assets,$group);

        #i: Assign parse asset to assets collection
        $this->assets[$key] = $assets_common;

        #i: Return to repo dispatcher
        return $assets_common;
    }


    /**
     * Parse assets from array
     *
     * @param $assets
     * @param $group
     * @return array
     */
    protected function parseAssetsArray($assets,$group)
    {
        #i: Get default asset config
        $assets_default = isset($assets['default']) ? $assets['default'] : app('config')->get('core::asset.assets.default');

        #i: Construct mime class base collection class
        $asset_class = 'Atlantis\\Asset\\Collection\\'.studly_case($group);
        if( !class_exists($asset_class) ) return [];

        #i: Create an AssetCollection of current asset
        $assets_common = App::make($asset_class, [$assets[$group],[],$assets_default['path']]);

        return $assets_common;
    }


    /**
     * Determine if the given configuration group exists.
     *
     * @param  string $group
     * @param  string $namespace
     * @return mixed
     */
    public function exists($group, $namespace = null)
    {
        #i: Get key to check hints array
        $key = $this->getCollection($group,$namespace);

        if( isset($this->assets[$key]) ) return $this->assets[$key];

        #i: Check value in array
        if( is_null($namespace) ){
            $merge_assets = [];
            foreach($this->getNamespaces() as $namespace){
                $current_assets = array_get($this->hints,"$namespace.$group");
                $current_assets_path = array_get($this->hints,"$namespace.default.path");

                #i: Skip if asset key not exist
                if( is_null($current_assets) ) continue;

                foreach($current_assets as &$asset){
                    $asset = app('atlantis.helpers')->string->absolute_path($asset,false,$current_assets_path);
                }

                $merge_assets = array_merge_recursive($merge_assets, $current_assets);
            }

            $exists = [
                'default'   => app('config')->get('core::asset.assets.default'),
                $group      => $merge_assets
            ];

        }else{
            $exists = [
                'default'   => array_get($this->hints,"$namespace.default"),
                $group      => array_get($this->hints,"$namespace.$group")
            ];

            if( !isset($this->hints[$namespace]) ) $exists = null;
        }

        if( is_null($exists) )return $this->assets[$key] = false;

        return $exists;
    }


    /**
     * Add a new namespace to the loader.
     *
     * @param  string $namespace
     * @param  string $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        $this->hints[$namespace] = $hint;
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return array_keys($this->hints);
    }

    /**
     * Apply any cascades to an array of package options.
     *
     * @param  string $environment
     * @param  string $package
     * @param  string $group
     * @param  array $items
     * @return array
     */
    public function cascadePackage($environment, $package, $group, $items)
    {
    }


    /**
     * Get collection key
     *
     * @param $group
     * @param null $namespace
     * @return string
     */
    protected function getCollection($group, $namespace=null){
        $namespace = $namespace ?: '*';
        return $group.'::'.$namespace;
    }

}