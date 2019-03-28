<?php

namespace Zentefi\ConsoleCreateApi;

use Illuminate\Console\Command;

class ApiCreateCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make:api {className}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a table api';

	protected $_className;

	public function __construct()
	{
		parent::__construct();
	}


	public function handle()
	{
		$this->_className = $this->argument('className');
		echo "Api generated\n";
		$this->generateContract();
		$this->generateImplementation();
		$this->generateProvider();
		$this->addProviderApp();
		$this->call('optimize');
	}

	private function generateContract()
	{
		$path = base_path("app/Contracts/Apis/").$this->_className."Api.php";

		if(!file_exists($path))
		{
			$contents = "<?php namespace App\Contracts\Apis;\n\ninterface ".$this->_className."Api\n{\n\n}\n";
			@ mkdir(dirname($path), 0777, true);
			file_put_contents($path, $contents);
		}
	}

	private function generateImplementation()
	{
		$path = base_path("app/Domain/Apis/").$this->_className."Api.php";

		if(!file_exists($path))
		{
			$contents = "<?php namespace App\Domain\Apis;\n\nuse App\Contracts\Apis\\".$this->_className."Api as ".$this->_className."ApiInterface;\n\nclass ".$this->_className."Api implements ".$this->_className."ApiInterface\n{\n\n}\n";
			@ mkdir(dirname($path), 0777, true);
			file_put_contents($path, $contents);
		}
	}

	private function generateProvider()
	{

		$provider_code = "<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class {$this->_className}ApiProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        \$this->app->singleton(\App\Contracts\Apis\\".$this->_className."Api::class, \App\Domain\Apis\\".$this->_className."Api::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
    }
}
";
		$path = base_path("app/Providers/{$this->_className}ApiProvider.php");
		file_put_contents($path, $provider_code);
	}

	private function addProviderApp()
	{
		$class = "App\\Providers\\".$this->_className."ApiProvider::class";

		$app_path = base_path("config/app.php");
		$app_content = file_get_contents($app_path);

		if(strpos($app_content, $class) === false)
		{
			$app_content = preg_replace("#(?i)(?s)'providers'\s*\=\>\s*\[(.*?)]#", "'providers' => [\n\t\t{$class}, $1] ", $app_content);
			file_put_contents($app_path, $app_content);
		}

	}
}