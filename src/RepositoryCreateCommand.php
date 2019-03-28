<?php

namespace Zentefi\ConsoleCreateRepository;

use Illuminate\Console\Command;

class RepositoryCreateCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make:repository {className}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a table repository';

	protected $_className;

	public function __construct()
	{
		parent::__construct();
	}


	public function handle()
	{
		$this->_className = $this->argument('className');
		echo "Repository generated\n";
		$this->generateContract();
		$this->generateImplementation();
		$this->generateProvider();
		$this->addProviderApp();
		$this->call('optimize');
	}

	private function generateContract()
	{
		$path = base_path("app/Repositories/Contracts/").$this->_className."Repository.php";

		if(!file_exists($path))
		{
			$contents = "<?php namespace App\Repositories\Contracts;\n\ninterface ".$this->_className."Repository\n{\n\n}\n";
			@ mkdir(dirname($path), 0777, true);
			file_put_contents($path, $contents);
		}
	}

	private function generateImplementation()
	{
		$path = base_path("app/Repositories/Implementations/").$this->_className."RepositoryDatabase.php";

		if(!file_exists($path))
		{
			$contents = "<?php namespace App\Repositories\Implementations;\n\nuse App\Repositories\Contracts\\".$this->_className."Repository;\n\nclass ".$this->_className."RepositoryDatabase implements ".$this->_className."Repository\n{\n\n}\n";
			@ mkdir(dirname($path), 0777, true);
			file_put_contents($path, $contents);
		}
	}

	private function generateProvider()
	{

		$provider_code = "<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class {$this->_className}RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        \$this->app->singleton(\App\Repositories\Contracts\\".$this->_className."Repository::class, \App\Repositories\Implementations\\".$this->_className."RepositoryDatabase::class);
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
		$path = base_path("app/Providers/{$this->_className}RepositoryProvider.php");
		file_put_contents($path, $provider_code);
	}

	private function addProviderApp()
	{
		$class = "App\\Providers\\".$this->_className."RepositoryProvider::class";

		$app_path = base_path("config/app.php");
		$app_content = file_get_contents($app_path);

		if(strpos($app_content, $class) === false)
		{
			$app_content = preg_replace("#(?i)(?s)'providers'\s*\=\>\s*\[(.*?)]#", "'providers' => [\n\t\t{$class}, $1] ", $app_content);
			file_put_contents($app_path, $app_content);
		}
	}
}
