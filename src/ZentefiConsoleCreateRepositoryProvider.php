<?php

namespace Zentefi\ConsoleCreateRepository;

use Illuminate\Support\ServiceProvider;

class ZentefiConsoleCreateRepositoryProvider extends ServiceProvider
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
				RepositoryCreateCommand::class,
			]);
		}
    }
}
