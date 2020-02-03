<?php namespace Quasar\Core\Services;

use Quasar\Core\Traits\ValidatesGraphQL;
use Quasar\Core\Services\SQLService;

abstract class CoreService
{
    use ValidatesGraphQL;

    public function delete($uuid, $model, $commonUuid = null, $langClass)
    {
        $object = SQLService::deleteRecord($uuid, $model, $commonUuid, $langClass);

        return $object;
    }
}
