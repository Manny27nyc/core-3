<?php namespace Atlantis\Asset\Collection;

use Atlantis\Asset\Assetic\AssetCollection;
use Assetic\Filter\LessphpFilter;
use Atlantis\Asset\Assetic\Filter\CSSminFilter;
use Atlantis\Asset\Assetic\Filter\UriRewriteFilter;
use Atlantis\Asset\Assetic\Filter\UriPrependFilter;


class Stylesheet extends AssetCollection{

    public function __construct(array $assets=[], array $filters=[], $base_path=''){
        #i: Minified on production environment only
        if( in_array(app()->environment(), app('config')->get('core::asset.cache.environment')) ) $filters[] = new CSSminFilter();

        parent::__construct($assets,$filters,$base_path);
    }


    /**
     * Additional stylesheet processing
     *
     * @param \Assetic\Asset\AssetInterface $asset
     * @return \Assetic\Asset\AssetInterface
     */
    protected function filterComposer($asset){
        $base_url = app('config')->get('app.url');
        $file_ext = $this->files->extension($asset->getSourcePath());

        #i: Uri filter
        $asset->ensureFilter(new UriRewriteFilter());
        $asset->ensureFilter(new UriPrependFilter($base_url));

        #i: Apply filter based on extension
        if( $file_ext == 'less' ){
            $asset->setTargetPath( str_replace($file_ext,'css',$asset->getSourcePath()) );
            $asset->ensureFilter(new LessphpFilter());
        }

        return $asset;
    }

}