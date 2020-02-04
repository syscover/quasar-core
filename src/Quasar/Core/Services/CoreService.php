<?php namespace Quasar\Core\Services;

use Quasar\Core\Traits\ValidatesGraphQL;
use Quasar\Core\Services\SQLService;

abstract class CoreService
{
    use ValidatesGraphQL;

    public function delete($uuid, $modelClassName)
    {
        $object = SQLService::deleteRecord($uuid, $modelClassName);

        return $object;
    }
}
