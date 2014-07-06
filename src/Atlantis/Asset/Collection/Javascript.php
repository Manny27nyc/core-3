<?php namespace Atlantis\Asset\Collection;

use Assetic\Filter\JSMinFilter;


class Javascript extends AssetCollection{

    public function __construct(array $assets=[], array $filters=[], $base_path=''){

        if(!app('config')->get('app.debug'))  $filters[] = new JSMinFilter();

        parent::__construct($assets,$filters,$base_path);
    }

}