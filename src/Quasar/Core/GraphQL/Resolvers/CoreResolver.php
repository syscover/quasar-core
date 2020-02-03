<?php namespace Quasar\Core\GraphQL\Resolvers;

use Quasar\Core\Services\SQLService;

abstract class CoreResolver
{
    protected $model;
    protected $service;

    public function get($root, array $args)
    {
        $query = $this->model->builder();

        if(isset($args['query']))
        {
            $query = SQLService::makeQueryBuilder($query, $args['query']);
            $query = SQLService::makeQueryBuilderOrderedAndLimited($query, $args['query']);
        }

        return $query->get();
    }

    public function paginate($root, array $args)
    {
        return (Object) [
            'queryBuilder' => $this->model->calculateFoundRows()->builder()
        ];
    }

    public function find($root, array $args)
    {
        $query = SQLService::makeQueryBuilder($this->model->builder(), $args['query']);

        return $query->first();
    }

    public function create($root, array $args)
    {
        return $this->service->create($args['payload']);
    }

    public function update($root, array $args)
    {
        return $this->service->update($args['payload'], $args['payload']['uuid']);
    }

    public function delete($root, array $args)
    {
        return $this->service->delete($args['uuid'], get_class($this->model), $args['commonUuid'] ?? null, $args['langClass'] ?? null);
    }
}
