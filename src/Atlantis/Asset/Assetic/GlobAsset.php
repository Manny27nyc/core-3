<?php

namespace Atlantis\Asset\Assetic;

use Assetic\Asset\FileAsset;
use Assetic\Util\VarUtils;
use Assetic\Filter\FilterInterface;


class GlobAsset extends \Assetic\Asset\AssetCollection{
    /** @var array Globs item */
    private $globs;

    /** @var bool Init flag */
    private $initialized;


    /**
     * Contructor
     *
     * @param array $globs
     * @param array $filters
     * @param null $root
     * @param array $vars
     */
    public function __construct($globs, $filters = array(), $root = null, array $vars = array())
    {
        $this->globs = (array) $globs;
        $this->initialized = false;

        parent::__construct(array(), $filters, $root, $vars);
    }


    /**
     * Return all
     *
     * @return array
     */
    public function all()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::all();
    }


    /**
     * Load asset
     *
     * @param FilterInterface $additionalFilter
     */
    public function load(FilterInterface $additionalFilter = null)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        parent::load($additionalFilter);
    }


    /**
     * Dump asset
     *
     * @param FilterInterface $additionalFilter
     * @return string
     */
    public function dump(FilterInterface $additionalFilter = null)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::dump($additionalFilter);
    }


    /**
     * Get last modified date
     *
     * @return int|null
     */
    public function getLastModified()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::getLastModified();
    }


    /**
     * Get iterator
     *
     * @return \RecursiveIteratorIterator|\Traversable
     */
    public function getIterator()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::getIterator();
    }


    /**
     * Set values
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        parent::setValues($values);
        $this->initialized = false;
    }


    /**
     * Initialize
     *
     */
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