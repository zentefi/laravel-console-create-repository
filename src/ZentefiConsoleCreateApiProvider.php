<?php

namespace Zentefi\ConsoleCreateApi;

use Illuminate\Support\ServiceProvider;

class ZentefiConsoleCreateApiProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
		if ($this->app->runningInConsole()) {
			$this->commands([
				ApiCreateCommand::class,
			]);
		}
    }
}
