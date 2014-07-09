<?php namespace Atlantis\Asset\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;


class CSSminFilter implements FilterInterface
{

    public function filterLoad(AssetInterface $asset){}

    public function filterDump(AssetInterface $asset)
    {
        $cssmin = new \CSSmin();
        $asset->setContent($cssmin->run($asset->getContent()));
    }
}