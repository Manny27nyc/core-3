<?php

namespace Atlantis\Asset\Collection;

use Atlantis\Asset\Assetic\AssetCollection;
use Assetic\Filter\JSMinFilter;


class Javascript extends AssetCollection{

    public function __construct(array $assets=[], array $filters=[], $base_path=''){
        /** Minified on production environment only */
        if( in_array(app()->environment(), app('config')->get('core::asset.cache.environment')) )  $filters[] = new JSMinFilter();

        parent::__construct($assets,$filters,$base_path);
    }

}