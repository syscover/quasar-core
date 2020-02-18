<?php namespace Quasar\Core;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class CoreServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot(ConfigRepository $configRepository)
	{
        // register config
        $this->publishes([
            __DIR__ . '/../../config/lighthouse.php'                    => config_path('lighthouse.php'),
            __DIR__ . '/../../config/quasar-core.php'                   => config_path('quasar-core.php'),
            __DIR__ . '/../../../../oauth/src/config/quasar-oauth.php'  => config_path('quasar-oauth.php')
        ], 'config');

        // register seeds
        $this->publishes([
            __DIR__ . '/../../../../oauth/src/database/seeds/'          => base_path('/database/seeds')
        ], 'seeds');

        // publish schema
        $this->publishes([
            __DIR__ . '/../../config/schema.graphql' => $configRepository->get('lighthouse.schema.register')
        ], 'schema');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
        //
	}
}
