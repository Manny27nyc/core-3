<?php namespace Atlantis\Asset\Collection;

use Assetic\Filter\LessphpFilter;
use Assetic\Filter\CssMinFilter;
use Atlantis\Asset\Assetic\Filter\UriRewriteFilter;
use Atlantis\Asset\Assetic\Filter\UriPrependFilter;


class Stylesheet extends AssetCollection{

    public function __construct(array $assets=[], array $filters=[], $base_path=''){
        #i:::: Global filters
        $filters = [
            new UriRewriteFilter(),
            new UriPrependFilter()
        ];

        if(!app('config')->get('app.debug'))  $filters[] = new CssMinFilter();

        parent::__construct($assets,$filters,$base_path);
    }


    protected function filterComposer($asset){
        #i: Apply filter based on extension
        if( str_contains($asset->getSourcePath(),'less') ){
            $asset->ensureFilter(new LessphpFilter());
        }

        return $asset;
    }

}