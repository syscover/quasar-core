<?php namespace Quasar\Core\GraphQL\Resolvers;

use Quasar\Core\Services\SlugService;

class SlugResolver
{
    public function index($root, array $args)
    {
        return SlugService::checkSlug($args['model'], $args['slug'], $args['uuid'] ?? null, $args['column'] ?? 'slug', $args['langUuid'] ?? null);
    }
}