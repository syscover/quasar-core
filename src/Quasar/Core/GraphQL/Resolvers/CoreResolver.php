<?php namespace Quasar\Core\GraphQL\Resolvers;

abstract class CoreResolver
{
    protected $model;
    protected $service;

    public function get($root, array $args)
    {
        return $this->service->get($args, $this->model);
    }

    public function paginate($root, array $args)
    {
        return (Object) [
            'queryBuilder' => $this->model->calculateFoundRows()->builder()
        ];
    }

    public function find($root, array $args)
    {
        return $this->service->find($args, $this->model);
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
        return $this->service->delete($args, $this->model);
    }
}
