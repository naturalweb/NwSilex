<?php
namespace NwSilexTest\Provider;

use NwSilex\Foundation\Application;
use NwSilex\Provider\EloquentOrmServiceProvider;

class EloquentOrmServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigServiceProviderWithOnlyGlobal()
    {
        $app = new Application();
        $app['app.config'] = array(
            'db' => array(
                'default' => 'sqlite',
                'connections' => array(
                    'sqlite' => array(
                        'driver'   => 'sqlite',
                        'database' => __DIR__.'/_files/database.sqlite',
                    ),
                ),
            ),
        );

        $app->register(new EloquentOrmServiceProvider());
        $app->boot();

        $this->assertInstanceOf('Illuminate\Database\ConnectionResolver', $app['db.resolver']);
        $this->assertInstanceOf('Illuminate\Database\SQLiteConnection', $app['db']);
        $this->assertEquals($app['db.resolver'], \Illuminate\Database\Eloquent\Model::getConnectionResolver());
    }
}