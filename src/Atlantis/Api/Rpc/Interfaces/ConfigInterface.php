<?php

namespace Atlantis\Api\Rpc\Interfaces;

interface ConfigInterface {
    public function getRoutePrefix();
    public function getResolutionPattern();
    public function getResolver();
} 