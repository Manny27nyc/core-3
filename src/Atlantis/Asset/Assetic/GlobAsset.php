<?php namespace Atlantis\Asset\Assetic;

use Assetic\Asset\FileAsset;
use Assetic\Util\VarUtils;
use Assetic\Filter\FilterInterface;


class GlobAsset extends \Assetic\Asset\AssetCollection{
    private $globs;
    private $initialized;

    public function __construct($globs, $filters = array(), $root = null, array $vars = array())
    {
        $this->globs = (array) $globs;
        $this->initialized = false;

        parent::__construct(array(), $filters, $root, $vars);
    }

    public function all()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::all();
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        parent::load($additionalFilter);
    }

    public function dump(FilterInterface $additionalFilter = null)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::dump($additionalFilter);
    }

    public function getLastModified()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::getLastModified();
    }

    public function getIterator()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::getIterator();
    }

    public function setValues(array $values)
    {
        parent::setValues($values);
        $this->initialized = false;
    }

    private function initialize()
    {
        foreach ($this->globs as $glob) {
            $glob = VarUtils::resolve($glob, $this->getVars(), $this->getValues());

            if (false !== $paths = glob($glob,GLOB_BRACE)) {

                foreach ($paths as $path) {
                    if (is_file($path)) {
                        $this->add(new FileAsset($path, array(), $this->getSourceRoot()));
                    }
                }
            }
        }

        $this->initialized = true;
    }
}