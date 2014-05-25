<?php namespace Atlantis\Core\Config;

use Illuminate\Config\Repository as BaseRepository;


class Repository extends BaseRepository{

    public function override($namespace,$group,$path){
        #i: Add override namespace
        $this->loader->addNamespace($namespace,$path);

        #i: Load config
        $configs = $this->loader->load($this->environment,$group,$namespace);

        #i: Apply config override
        $this->set("$namespace::$group",$configs);
   }

}