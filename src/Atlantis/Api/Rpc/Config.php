<?php
namespace Atlantis\Api\Rpc;

use Illuminate\Support\Facades\Config as BaseConfig;
use Atlantis\Api\Rpc\Interfaces\ConfigInterface;


class Config implements ConfigInterface
{
    const DEFAULT_RESOLUTION_PATTERN = '\\{class}\\{{class}}Controller';

    public function getResolutionPattern()
    {
        return BaseConfig::get('api::rpc.resolution_pattern', self::DEFAULT_RESOLUTION_PATTERN);
    }

    public function getRoutePrefix()
    {
        return BaseConfig::get('api::rpc.route_prefix');
    }

    public function getResolver()
    {
        return BaseConfig::get('api::rpc.resolver');
    }

    public function getExceptionHandler()
    {
        return BaseConfig::get('api::rpc.exception_handler');
    }
}