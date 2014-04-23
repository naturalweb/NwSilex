<?php
namespace NwSilex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\ConnectionResolver;

class EloquentOrmServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['db.default'] = '';

        $app['db.container'] = $app->share(function($app) {
			return new Container;
		});

		$app['db.factory'] = $app->share(function($app) {
			return new ConnectionFactory($app['db.container']);
		});

		$app['db.resolver'] = $app->share(function($app) {
			$resolver = new ConnectionResolver();

			$config = isset($app['app.config']['db']) ? $app['app.config']['db'] : array();

			if (isset($config['connections'])) {
				foreach ($config['connections'] as $name => $conn) {
					$connection = $app['db.factory']->make($conn);
					if (! $app['debug']) {
						$connection->disableQueryLog();
					}
					$resolver->addConnection($name, $connection);
				}
			}

			if (isset($config['default'])) {
				$app['db.default'] = $config['default'];
			}
			
			$resolver->setDefaultConnection($app['db.default']);
			return $resolver;
		});

		$app['db'] = $app->share(function($app) {
			return $app['db.resolver']->connection($app['db.default']);
		});
    }

    public function boot(Application $app)
    {
    	\Illuminate\Database\Eloquent\Model::setConnectionResolver($app['db.resolver']);
    }
}