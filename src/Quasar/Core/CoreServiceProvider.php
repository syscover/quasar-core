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
            __DIR__ . '/../../config/lighthouse.php' => config_path('lighthouse.php'),
            __DIR__ . '/../../config/quasar-core.php' => config_path('quasar-core.php')
        ], 'config');

        // publish schema
        $this->publishes([
            __DIR__ . '/../../config/schema.graphql' => $configRepository->get('lighthouse.schema.register'),
        ], 'schema');

        // publish test
        $this->publishes([
            __DIR__ . '/../../tests/Unit' => base_path('tests/Unit'),
        ], 'tests');
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
