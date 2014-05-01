<?php
namespace NwSilexTest\Provider;

use NwSilex\Foundation\Application;
use NwSilex\Provider\ConfigServiceProvider;

class ConfigServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!defined('APP_PATH')) {
            define('APP_PATH', __DIR__ . '/_files');
        }
    }

    public function testConfigServiceProviderWithOnlyGlobal()
    {
        $app = new Application();
        $app['app.env'] = 'prod';
        $app->register(new ConfigServiceProvider());
        $app->boot();
        
        $this->assertTrue(is_array($app['app.config']));
        $this->assertEquals(1, count($app['app.config']));

        $this->assertTrue(is_array($app['app.config']['foo']));
        $this->assertEquals(1, count($app['app.config']['foo']));

        $this->assertTrue(isset($app['app.config']['foo']['bar']));
        $this->assertEquals('value_global', $app['app.config']['foo']['bar']);

    }

    public function testConfigServiceProviderWithEnvironment()
    {
        $app = new Application();
        $app['app.env'] = 'dev';
        $app->register(new ConfigServiceProvider());
        $app->boot();
        
        $this->assertEquals('value_environment', $app['app.config']['foo']['bar']);

    }
}