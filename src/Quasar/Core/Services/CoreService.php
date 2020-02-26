<?php namespace Quasar\Core\Services;

use Quasar\Core\Traits\ValidatesGraphQL;
use Quasar\Core\Services\SQLService;

abstract class CoreService
{
    use ValidatesGraphQL;

    public function get(array $data, $model)
    {
        $queryBuilder = $model->builder();

        if (isset($data['query']))
        {
            $queryBuilder = SQLService::makeQueryBuilder($queryBuilder, $data['query']);
            $queryBuilder = SQLService::makeQueryBuilderOrderedAndLimited($queryBuilder, $data['query']);
        }

        return $queryBuilder->get();
    }

    public function find(array $data, $model)
    {
        if (isset($data['query']))
        {
            $queryBuilder = SQLService::makeQueryBuilder($model->builder(), $data['query']);

            return $queryBuilder->first();
        }

        return null;
    }

    public function delete(array $data, $model)
    {
        $this->validate($data, [
            'uuid' => 'required|uuid|exists:' . $model->getTable() . ',uuid'
        ]);

        $object = SQLService::deleteRecord($data['uuid'], $model);

        return $object;
    }
}
