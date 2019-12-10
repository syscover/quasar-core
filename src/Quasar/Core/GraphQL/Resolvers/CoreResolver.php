<?php namespace Quasar\Core\GraphQL\Resolvers;

use Quasar\Core\Services\SQLService;

abstract class CoreResolver
{
    protected $model;
    protected $service;

    public function get($root, array $args)
    {
        $query = $this->model->builder();

        if(isset($args['sql']))
        {
            $query = SQLService::getQueryFiltered($query, $args['sql'], $args['filters'] ?? null);
            $query = SQLService::getQueryOrderedAndLimited($query, $args['sql'], $args['filters'] ?? null);
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
        $query = SQLService::getQueryFiltered($this->model->builder(), $args['sql'], $args['filters'] ?? null);

        return $query->first();
    }

    public function create($root, array $args)
    {
        return $this->service->create($args['payload']);
    }

    public function update($root, array $args)
    {
        return $this->service->update($args['payload'], $args['payload']['id']);
    }

    public function delete($root, array $args)
    {
        $object = SQLService::deleteRecord($args['id'], get_class($this->model), $args['lang_id'] ?? null, $args['lang_class'] ?? null);

        return $object;
    }
}
