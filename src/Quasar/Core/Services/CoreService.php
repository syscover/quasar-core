<?php namespace Quasar\Core\Services;

use Quasar\Core\Traits\ValidatesGraphQL;
use Quasar\Core\Services\SQLService;

abstract class CoreService
{
    use ValidatesGraphQL;

    public function delete(array $data, $model)
    {
        $this->validate($data, [
            'uuid' => 'required|uuid|exists:' . $model->getTable() . ',uuid'
        ]);

        $object = SQLService::deleteRecord($data['uuid'], $model);

        return $object;
    }
}
