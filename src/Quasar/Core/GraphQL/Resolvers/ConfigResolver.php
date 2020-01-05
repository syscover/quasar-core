<?php namespace Quasar\Core\GraphQL\Resolvers;

use Illuminate\Support\Facades\App;

class ConfigResolver
{
    public function index($root, array $args)
    {
        $config = config($args['config']['key']);

        if (!$config) throw new \Error('Config file "' . $args['config']['key'] . '" doesn\'t exist.');

        if(isset($args['config']['lang']) && isset($args['config']['property']))
        {
            // set lang
            App::setLocale($args['config']['lang']);
            $property = $args['config']['property'];

            // translate property indicated
            $config = array_map(function($object) use ($property) {
                $object->{$property} = trans($object->{$property});
                return $object;
            }, $config);
        }

        return $config;
    }
}